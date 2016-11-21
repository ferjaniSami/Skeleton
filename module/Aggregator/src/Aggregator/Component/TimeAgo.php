<?php
namespace Aggregator\Component;
class TimeAgo{
	
	/**
	 * les unités de temps à afficher
	 * @var array
	 */
	private static $_periodes = array('seconde', 'minute', 'heure', 'jour', 'semaine', 'mois', 'ann&eacute;e', 'decade');
	
	/**
	 * Longeur de chaque unite de temps exp decade = 10 annee, annee = 12 mois, mois = 4.35 semaines
	 * @var array
	 */
	private static $_lengths = array('60','60','24','7','4.35','12','10');
	
	/**
	 * prefixe de la date ago au passé
	 * @var string
	 */
	private static $_before = 'Il y a';
	
	/**
	 * prefixe de la date ago au futur.
	 * @var string
	 */
	private static $_after = 'après';
	
	//default message si date invalide
	private static $_defaultAgo = 'Il y a quelque temps';
	
	/**
	 * display the time like Twitter
	 *
	 * @param int(timestamp) $date
	 * @return string
	 */
	public static function timeAgo($date)
	{
		if(empty($date)) return self::$_defaultAgo;
		$periods = self::$_periodes;
		$lengths = self::$_lengths;
		$now = time();
		// is it future date or past date
		if($now > $date) {
			$difference = $now - $date;
			$tense = self::$_before;
		}else{
			$difference = $date - $now;
			$tense = self::$_after;
		}
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}
		$difference = round($difference);
		if($difference != 1 && $periods[$j] != 'mois')
			$periods[$j].= 's';
		return "{$tense} $difference $periods[$j]";
	}
}