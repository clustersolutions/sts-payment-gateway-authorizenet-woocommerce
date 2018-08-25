<?php

if (!class_exists('EH_Authorize_Net_Log')) {

    class EH_Authorize_Net_Log {

        public static function init_live_log() {
            $content = "<------------------- ExtensionHawk Authorize.Net Payment Live Log File ------------------->\n";
            return $content;
        }

        public static function init_dead_log() {
            $content = "<------------------- ExtensionHawk Authorize.Net Payment Dead Log File ------------------->\n";
            return $content;
        }

        public static function log_update($type, $msg, $title) {
            $check = get_option('woocommerce_eh_authorize_net_aim_card_settings');
            if ('yes' === $check['eh_anet_logging']) {
                if (WC()->version >= '2.7.0') {
                    $log = wc_get_logger();
                    $head = "<------------------- ExtensionHawk Authorize.Net Payment ( " . $title . " ) ------------------->\n";
                    switch ($type) {
                        case 'live':
                            $log_text = $head . print_r((object) $msg, true);
                            $live_context = array('source' => 'eh_authorize_net_pay_live');
                            $log->log("debug", $log_text, $live_context);
                            break;
                        case 'dead':
                            $log_text = $head . print_r((object) $msg, true);
                            $dead_context = array('source' => 'eh_authorize_net_pay_dead');
                            $log->log("debug", $log_text, $dead_context);
                            break;
                    }
                } else {
                    $log = new WC_Logger();
                    $head = "<------------------- ExtensionHawk Authorize.Net Payment ( " . $title . " ) ------------------->\n";
                    switch ($type) {
                        case 'live':
                            $log_text = $head . print_r((object) $msg, true);
                            $log->add("eh_authorize_net_pay_live", $log_text);
                            break;
                        case 'dead':
                            $log_text = $head . print_r((object) $msg, true);
                            $log->add("eh_authorize_net_pay_dead", $log_text);
                            break;
                    }
                }
            }
        }

    }

}
