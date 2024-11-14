<?php
namespace Administration\Components\Utilities;

defined( 'ABSPATH' ) || exit;

/**
 * Class Helpers
 *
 * Indeholder generelle hjælpefunktioner til pluginet.
 *
 * @package Administration\Components\Utilities
 */
class Helpers {

    /**
     * Singleton instance.
     *
     * @var Helpers
     */
    private static $instance = null;

    /**
     * Få singleton instance af klassen.
     *
     * @return Helpers
     */
    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor for at forhindre direkte instansiering.
     */
    private function __construct() {}

    /**
     * Saniterer et array rekursivt.
     *
     * @param array $array Arrayet der skal saniteres.
     * @return array Saniteret array.
     */
    public function sanitizeArray( $array ) {
        $sanitized = array();
        foreach ( $array as $key => $value ) {
            if ( is_array( $value ) ) {
                $sanitized[ $key ] = $this->sanitizeArray( $value );
            } else {
                $sanitized[ $key ] = sanitize_text_field( $value );
            }
        }
        return $sanitized;
    }

    /**
     * Tjekker om en streng er gyldig JSON.
     *
     * @param string $string Strengen der skal tjekkes.
     * @return bool True hvis gyldig JSON, false ellers.
     */
    public function isValidJson( $string ) {
        json_decode( $string );
        return ( json_last_error() === JSON_ERROR_NONE );
    }

    /**
     * Henter en værdi fra et array sikkert.
     *
     * @param array  $array Arrayet der hentes fra.
     * @param string $key Nøglen der skal hentes.
     * @param mixed  $default Standardværdi hvis nøglen ikke findes.
     * @return mixed Værdien.
     */
    public function getArrayValue( $array, $key, $default = null ) {
        return isset( $array[ $key ] ) ? $array[ $key ] : $default;
    }

    /**
     * Opdaterer konstanter i wp-config.php med nye værdier.
     *
     * @param array $data Assoc-array af konstantnavne og værdier.
     * @return bool True ved succes, false ved fejl.
     */
    public function updateConfigConstants( $data ) {
        $config_file = ABSPATH . 'wp-config.php';

        if ( ! file_exists( $config_file ) || ! is_writable( $config_file ) ) {
            return false;
        }

        $config_contents = file_get_contents( $config_file );

        foreach ( $data as $constant => $value ) {
            $pattern = "/define$$\s*'{$constant}'.*$$;/";
            $replacement = "define( '{$constant}', '{$value}' );";

            if ( preg_match( $pattern, $config_contents ) ) {
                $config_contents = preg_replace( $pattern, $replacement, $config_contents );
            } else {
                $config_contents = preg_replace(
                    "/(\/\*\* Sets up WordPress vars and included files\. \*\/)/",
                    $replacement . "\n$1",
                    $config_contents
                );
            }
        }

        return file_put_contents( $config_file, $config_contents ) !== false;
    }

    /**
     * Henter specifikke konstanter fra wp-config.php.
     *
     * @param array $constants Array af konstantnavne der skal hentes.
     * @return array Assoc-array af konstantsnavne og deres værdier.
     */
    public function getConfigConstants( $constants ) {
        $result = array();
        foreach ( $constants as $constant ) {
            if ( defined( $constant ) ) {
                $result[ $constant ] = constant( $constant );
            }
        }
        return $result;
    }
}
