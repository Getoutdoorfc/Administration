<?php
namespace Administration\Components\Utilities;

use DateTime;

defined( 'ABSPATH' ) || exit;

/**
 * Class DateHelper
 *
 * Hjælpeklasse til datofunktioner og formatering.
 *
 * @package Administration\Components\Utilities
 */
class DateHelper {

    /**
     * Konverterer en dato til ISO 8601 format.
     *
     * @param string $date Dato streng.
     * @return string Formateret dato streng.
     */
    public function toIso8601( $date ) {
        try {
            $datetime = new DateTime( $date );
            return $datetime->format( 'c' ); // ISO 8601 format.
        } catch ( \Exception $e ) {
            return '';
        }
    }

    /**
     * Beregner sluttidspunkt baseret på starttidspunkt og varighed.
     *
     * @param string $start_time Starttidspunkt i 'Y-m-d\TH:i' format.
     * @param float  $duration Varighed i timer.
     * @return string Sluttidspunkt i 'Y-m-d\TH:i' format.
     */
    public function calculateEndTime( $start_time, $duration ) {
        $datetime = DateTime::createFromFormat( 'Y-m-d\TH:i', $start_time );
        if ( $datetime === false ) {
            return '';
        }
        $minutes = $duration * 60;
        $datetime->modify( "+{$minutes} minutes" );
        return $datetime->format( 'Y-m-d\TH:i' );
    }

    /**
     * Validerer datoformatet.
     *
     * @param string $date Dato streng.
     * @param string $format Forventet datoformat.
     * @return bool True hvis gyldig, false ellers.
     */
    public function validateDateFormat( $date, $format = 'Y-m-d\TH:i' ) {
        $d = DateTime::createFromFormat( $format, $date );
        return $d && $d->format( $format ) === $date;
    }
}
