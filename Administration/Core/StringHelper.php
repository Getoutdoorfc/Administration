<?php
namespace Administration\Components\Utilities;

defined( 'ABSPATH' ) || exit;

/**
 * Class StringHelper
 *
 * Hjælpeklasse til strengmanipulation.
 *
 * @package Administration\Components\Utilities
 */
class StringHelper {

    /**
     * Trunkerer en streng til en bestemt længde.
     *
     * @param string $string Strengen der skal trunkeres.
     * @param int    $length Ønsket længde.
     * @param string $append Tekst der skal tilføjes efter trunkeringen.
     * @return string Den trunkerede streng.
     */
    public function truncate( $string, $length = 100, $append = '...' ) {
        if ( mb_strlen( $string ) <= $length ) {
            return $string;
        }
        return mb_substr( $string, 0, $length ) . $append;
    }

    /**
     * Saniterer en streng.
     *
     * @param string $string Strengen der skal saniteres.
     * @return string Den saniterede streng.
     */
    public function sanitize( $string ) {
        return sanitize_text_field( $string );
    }

    /**
     * Genererer en tilfældig streng.
     *
     * @param int $length Længden af den tilfældige streng.
     * @return string Tilfældig streng.
     */
    public function randomString( $length = 16 ) {
        return bin2hex( random_bytes( $length / 2 ) );
    }
}
