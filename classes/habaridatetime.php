<?php
// Unix time is UTC timezone _always_
class HabariDateTime extends DateTime
{
	public function modify($modify)
	{
		parent::modify($modify);
		return $this;
	}

	public function setDate($year, $month, $day)
	{
		parent::setDate($year, $month, $day);
		return $this;
	}

	public function setISODate($year, $week, $day = null)
	{
		parent::setISODate($year, $week, $day);
		return $this;
	}

	public function setTime($hour, $minute, $second = null)
	{
		parent::setTime($hour, $minute, $second);
		return $this;
	}

	public function setTimezone($timezone)
	{
		parent::setTimezone($timezone);
		return $this;
	}

	public function format($format = null)
	{
		if ($format === null) {
			$format = Locale::get_date_format() . ' ' . Locale::get_time_format();
		}
		return parent::format($format);
	}

	public function get($format = null)
	{
		return $this->format($format);
	}

	public function out($format = null)
	{
		echo $this->format($format);
	}

	static function date_create($time = null, $timezone = null)
	{
		if ($time instanceOf HabariDateTime) {
			return $time;
		}
		elseif ($time instanceOf DateTime) {
			$time = $time->format('U');
		}

		if ($timezone === null) {
			$timezone = Locale::get_timezone();
		}

		if ($time != '') {
			// Should we limit the number of integer in the Unix timestamp?
			if (preg_match('%[0-9]+%', $time)) {
				$time = '@' . $time;
			}
		}

		$datetime = new HabariDateTime($time);
		$datetime->setTimezone(new DateTimeZone($timezone));
		return $datetime;
	}

	public function __toString()
	{
		return $this->format('U');
	}

	public function __get($property)
	{
		switch ($property) {
			case 'clone':
				return clone $this;

			case 'sql':
				return $this->format('Y-m-d H:i:s');
				break;

			default:
				$info = getdate($this->format('U'));
				$info['mon0'] = substr('0' . $info['mon'], -2, 2);
				$info['mday0'] = substr('0' . $info['mday'], -2, 2);
				if(isset($info[$property])) {
					return $info[$property];
				}
				return $this->$property;
		}
	}

	public function getdate()
	{
		$info = getdate($this->format('U'));
		$info['mon0'] = substr('0' . $info['mon'], -2, 2);
		$info['mday0'] = substr('0' . $info['mday'], -2, 2);
		return $info;
	}
}

?>