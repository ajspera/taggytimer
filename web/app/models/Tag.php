<?

class Tag extends Eloquent {

	protected $table = 'tags';
	
	protected $guarded = array(
		'id',
		'tag_title'
	);
	
	public $timestamps = false;
	
	public $rangeStart = null;
	public $rangeEnd = null;
	
	public function users()
	{
		return $this->belongsToMany('User', 'tags_users')->withTimestamps();
	}
	
	public function timersAss()
	{
		return $this->belongsToMany('Timer', 'tags_timers');
	}
	
	public function timers()
	{
		$userId = Auth::user()->id;
		if(!$this->rangeEnd || !is_numeric($this->rangeEnd))
			$this->rangeEnd = time();
		if(!$this->rangeStart || !is_numeric($this->rangeStart))
			$this->rangeStart = 1;
		
		$obj = $this->belongsToMany('Timer', 'tags_timers')
    		->join(DB::raw('timers as first_timer'), 'first_timer.id', '=', 'tags_timers.first_timer_id')
    		->join(DB::raw('timers as timer'), 'timer.id', '=', 'tags_timers.timer_id')
    		->leftJoin(DB::raw('timers as last_timer'), 'last_timer.id', '=', 'tags_timers.last_timer_id')
    		->where('timers.user_id', '=', $userId)
			->groupBy('tags_timers.first_timer_id');
		$obj->where(function($q){
			$q->whereNull('last_timer.end')
				->orWhere('last_timer.end', '>', $this->rangeStart);
				
		});
		$obj->where(function($q){
			$q->whereNull('first_timer.start')
				->orWhere('first_timer.start', '<', $this->rangeEnd);
				
		});
/*
		$select = 
'`timers`.*, `tags_timers`.`tag_id` as `pivot_tag_id`, `tags_timers`.`timer_id` as `pivot_timer_id`,
first_timer.start as pivot_start, last_timer.end as pivot_end,
case
when last_timer.end IS NULL then UNIX_TIMESTAMP() - first_timer.start
else sum(timer.duration)
end as pivot_block_duration';
*/
		$select = 
"`timers`.*, `tags_timers`.`tag_id` as `pivot_tag_id`, `tags_timers`.`timer_id` as `pivot_timer_id`,
first_timer.start as pivot_start, last_timer.end as pivot_end,
IF( last_timer.end IS NULL OR last_timer.end > '$this->rangeEnd', '$this->rangeEnd', last_timer.end ) - IF( first_timer.start > '$this->rangeStart', first_timer.start, $this->rangeStart )
as pivot_block_duration";
		
		$obj->addSelect(DB::raw($select));
		
		return $obj;
			
	}
	
	public static function getTitle($title, $columns = null)
	{
		$tag = self::where('tag_title', '=', $title)->first($columns);
		if(empty($tag->id)){
			$tag = new self;
			$tag->tag_title = $title;
			$tag->save();
		}
		return $tag;

	}
	public function duration($since = null)
	{
		// Easily populate the durration of a tag's timer
		$userId = Auth::user()->id;
		if(!is_numeric($since))
			$since = 1385425000;
		$duration = 0;
		if(isset($this->toArray()['timers'])){
			$duration = 0;
			foreach($this->toArray()['timers'] as $timer){
				$duration += $timer['pivot']['block_duration'];
			}
		} else if($this->rangeStart || $this->rangeEnd) {
			foreach($this->timers()->get()->toArray() as $timer){
				$duration += $timer['pivot']['block_duration'];
			}
		} else {
			/*
$duration = $this->timersAss()
				->join(DB::raw('timers as first_timer'), 'first_timer.id', '=', 'tags_timers.first_timer_id')
				->select(DB::raw('SUM( IF( timers.duration IS NULL, UNIX_TIMESTAMP() - first_timer.start, timers.duration ) ) as duration'))->get()[0]->duration;
			
*/
			$this->attributes['retrievedAt'] = time();
			$duration = DB::table('tags_timers')
				->join(DB::raw('timers as timer'), 'timer.id', '=', 'tags_timers.timer_id')
				->select(DB::raw("SUM( IF( timer.duration IS NULL, UNIX_TIMESTAMP() - IF(timer.start < '$since', '$since', timer.start) , IF(timer.start < '$since', timer.end - '$since', timer.duration ) ) ) as duration, tags_timers.tag_id as id, SUM( IF( timer.duration IS NULL, 1, 0 ) ) as running"))
				->where('tags_timers.tag_id', $this->id)
				->where(function($q) use ($since){
					$q->where('timer.end', '=', 0)
						->orWhere('timer.end', '>', $since);
						
				})
				->where('tags_timers.user_id', '=', $userId)
				->groupBy('tags_timers.tag_id')
				->get();
			$this->attributes['retrievedAt'] = time();
			if(!empty($duration[0])) {
				$this->attributes['duration'] = (integer) $duration[0]->duration;
				$this->attributes['running'] = (boolean) $duration[0]->running;
			} else {
				$this->attributes['duration'] = 0;
				$this->attributes['running'] = false;
			}
		}
		
	}
	public static function collectionDuration(&$collection, $since = null)
	{
		if(count($collection) == 0)
			return $collection;
		
		$userId = Auth::user()->id;
		if(!is_numeric($since))
			$since = 1385425000;
		$in = array();
		foreach($collection as $tag){
			$in[] = $tag->id;
		}
		$duration = DB::table('tags_timers')
				->join(DB::raw('timers as timer'), 'timer.id', '=', 'tags_timers.timer_id')
				->select(DB::raw("SUM( IF( timer.duration IS NULL, UNIX_TIMESTAMP() - IF(timer.start < '$since', '$since', timer.start) , IF(timer.start < '$since', timer.end - '$since', timer.duration ) ) ) as duration, tags_timers.tag_id as id, SUM( IF( timer.duration IS NULL, 1, 0 ) ) as running"))
				->whereIn('tags_timers.tag_id', $in)
				->where(function($q) use ($since){
					$q->where('timer.end', '=', 0)
						->orWhere('timer.end', '>', $since);
						
				})
				->where('tags_timers.user_id', '=', $userId)
				->orderBy('timer.end', 'asc')
				->groupBy('tags_timers.tag_id')
				->get();
		$durSet = array();
		foreach($duration as $dur)
		{
			$durSet[$dur->id] = $dur->duration;
			$runSet[$dur->id] = $dur->running;
		}
		foreach($collection as $key => $tag)
		{
			$collection[$key]->attributes['retrievedAt'] = time();
			if(isset($durSet[$tag->id])){
				$collection[$key]->attributes['duration'] = (integer) $durSet[$tag->id];
				$collection[$key]->attributes['running'] = (boolean) $runSet[$tag->id];
			} else {
				$collection[$key]->attributes['duration'] = 0;
				$collection[$key]->attributes['running'] = false;
			}
		}
		return $collection;
		
	}

}