<?
/* 
get duration blocks: 
select *, case timers.end
when 0 then UNIX_TIMESTAMP() - timers.start
else sum(duration)
    end as block_duration from tags_timers inner join timers where timers.id = tags_timers.first_timer_id AND tag_id = 14 group by tags_timers.first_timer_id;
 
    

select tags_timers.*, first_timer.start as start, last_timer.end as end,
case
when last_timer.end IS NULL then UNIX_TIMESTAMP() - first_timer.start
else sum(timer.duration)
    end as block_duration
from tags_timers inner join timers as timer on tags_timers.timer_id = timer.id inner join timers as first_timer on first_timer.id = tags_timers.first_timer_id left outer join timers as last_timer on last_timer.id = tags_timers.last_timer_id WHERE tag_id = 22 group by tags_timers.first_timer_id;
    

multi-tag duration
select *, case timers.end
when 0 then UNIX_TIMESTAMP() - timers.start
else sum(duration)
    end as block_duration from tags_timers inner join timers where timers.id = tags_timers.first_timer_id AND tag_id in (14, 15) HAVING COUNT(DISTINCT tag_id) = 2;
    
get duration for time frame
*/

class Timer extends Eloquent {

	protected $table = 'timers';
	
	protected $guarded = array(
		'id',
		'user_id'
	);
	
	protected $fillable = array(
		'start',
		'end',
		'duration'
	);
	
	private $oldTimer = null;
		
	public function tags()
	{
		return $this->belongsToMany('Tag', 'tags_timers')
			->join(DB::raw('timers as first_timer'), 'first_timer.id', '=', 'tags_timers.first_timer_id')
			->orderBy('first_timer.start', 'desc')
			->withPivot('first_timer_id', 'last_timer_id', 'user_id');
	}
	
	public static function addTags($tags = array(), $time = null)
	{
		$userId = Auth::user()->id;
		$obj = self::addBreak($time);
		$existing = array();
		foreach($obj->tags as $oldTag){
			$existing[$oldTag->id] = true;
		}
		foreach($tags as $tag){
			$tag = Tag::getTitle($tag);
			if(!isset($existing[$tag->id])){
				$extraInfo = array(
					'first_timer_id' => $obj->id,
					'last_timer_id' => null,
					'user_id' => $userId
				);
				$obj->tags()->attach($tag, $extraInfo);
			}
		}
		return $tag;
	}
	
	public static function removeTags($tags = array(), $time = null, $string = false)
	{
		$obj = self::addBreak($time);
		$existing = array();
		foreach($obj->tags as $key => $oldTag){
			$existing[$oldTag->id] = $key;
		}
		foreach($tags as $tag){
			
			if($string)
				$tag = Tag::getTitle($tag);
			else
				$tag = Tag::find($tag);
			
			if($tag && isset($existing[$tag->id])){
				//print_r($obj->tags[$existing[$tag->id]]->pivot->first_timer_id);
				$tag->timersAss()
					->where('tags_timers.first_timer_id', '=', $obj->tags[$existing[$tag->id]]->pivot->first_timer_id)
					->update(array('last_timer_id' => $obj->id));
				$obj->tags()->detach($tag->id);
			}
		}
		return $obj;
	}
	
	public static function addBreak($time = null)
	{
		
		if(!$time)
			$time = time();
			
		$userId = Auth::user()->id;
		$deadZone = 10;
		
		// get most recent timer break
		$oldTimer = self::where('user_id', '=', $userId)->where('start', '<', $time+$deadZone)->orderBy('start', 'desc')->with(array('tags'))->first();
		$newTimer = new self;
		$newTimer->user_id = $userId;
		
		if(empty($oldTimer->id)){
			// no timer, first ever break for user
			$newTimer->start = $time;
			$newTimer->duration = null;
			$newTimer->save();$newTimer = self::where('user_id', '=', $userId)->where('start', '<', $time+$deadZone)->orderBy('start', 'desc')->with(array('tags'))->first();
		} else if($oldTimer->start+$deadZone <= $time) {
			// creates a new timer break duplicating all tags attached
			$oldTimer->end = $time;
			$oldTimer->duration = $oldTimer->end - $oldTimer->start;
			$oldTimer->save();
			$newTimer->start = $time;
			$newTimer->save();
			$syncDat = array();
			foreach($oldTimer->tags as $tag){
				$extraInfo = array(
					'first_timer_id' => $tag->pivot->first_timer_id,
					'last_timer_id' => $tag->pivot->last_timer_id,
					'user_id' => $tag->pivot->user_id
				);
				$syncDat[$tag->id] = $extraInfo;
			}
			if(!empty($syncDat))
				$newTimer->tags()->sync($syncDat);
			
			//$newTimer = self::find($newTimer->id);
		} else {
			// old break is still within time deadzone threshhold, just use that one so shit aint so cray
			$newTimer = $oldTimer;
		}
		return $newTimer;
	}
	
	public static function findTag($id, $since = 'current')
	{
		if($since == 'current'){
			$tag = Tag::with('timers')->find($id);
		} else {
			
		}
		return $tag;
	}

}