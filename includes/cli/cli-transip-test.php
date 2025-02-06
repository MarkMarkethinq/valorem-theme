<?php

if (defined('WP_CLI') && WP_CLI) {
    class TransIP_CLI_Command
    {
        /**
         * Test the API connection.
         *
         * ## OPTIONS
         *
         * [--login=<login>]
         * : The TransIP login name. Defaults to the value of the 'transip_api_login_name' environment variable.
         *
         * [--private-key=<private_key>]
         * : The private key for the TransIP API. Defaults to the value of the 'transip_api_private_key' environment variable.
         *
         * [--test]
         * : Whether to use test mode. In test mode, no actual changes will be made.
         *
         * ## EXAMPLES
         *
         *     wp transip test-connection
         *     wp transip test-connection --test
         *     wp transip test-connection --login=example_login --private-key="key"
         *
         * @param array $args
         * @param array $assoc_args
         */
        public function test_connection($args, $assoc_args)
        {
            $login = $assoc_args['login'] ?? $_ENV['transip_api_login_name'] ?? null;
            $privateKey = $assoc_args['private-key'] ?? $_ENV['transip_api_private_key'] ?? null;
            $testMode = isset($assoc_args['test']);

            // In test mode with no credentials, we can use the demo token
            if ($testMode && !$login && !$privateKey) {
                $transipApi = new TransIP_API('', '', true, true);
            } else {
                if (!$login) {
                    WP_CLI::error("Login is required. Either pass --login or set the 'transip_api_login_name' environment variable.");
                }

                if (!$privateKey) {
                    WP_CLI::error("Private key is required. Either pass --private-key or set the 'transip_api_private_key' environment variable.");
                }

                $transipApi = new TransIP_API($login, $privateKey, true, $testMode);
            }

            $result = $transipApi->testConnection();
            if ($result) {
                WP_CLI::success("API connection successful!" . ($testMode ? ' (Test Mode)' : ''));
            } else {
                WP_CLI::error("API connection failed!");
            }
        }

        /**
         * Get all domains.
         *
         * ## OPTIONS
         *
         * [--login=<login>]
         * : The TransIP login name. Defaults to the value of the 'transip_api_login_name' environment variable.
         *
         * [--private-key=<private_key>]
         * : The private key for the TransIP API. Defaults to the value of the 'transip_api_private_key' environment variable.
         *
         * ## EXAMPLES
         *
         *     wp transip get-all-domains
         *
         * @param array $args
         * @param array $assoc_args
         */
        public function get_all_domains($args, $assoc_args)
        {
            $login = $assoc_args['login'] ?? $_ENV['transip_api_login_name'] ?? null;
            $privateKey = $assoc_args['private-key'] ?? $_ENV['transip_api_private_key'] ?? null;

            if (!$login) {
                WP_CLI::error("Login is required. Either pass --login or set the 'transip_api_login_name' environment variable.");
            }

            if (!$privateKey) {
                WP_CLI::error("Private key is required. Either pass --private-key or set the 'transip_api_private_key' environment variable.");
            }

            $transipApi = new TransIP_API($login, $privateKey);

            $domains = $transipApi->getAllDomains();
            if (!empty($domains)) {
                WP_CLI::log("Domains:");
                foreach ($domains as $domain) {
                    WP_CLI::log("- $domain");
                }
            } else {
                WP_CLI::log("No domains found.");
            }
        }

        /**
         * Check if a domain is available.
         *
         * ## OPTIONS
         *
         * [--login=<login>]
         * : The TransIP login name. Defaults to the value of the 'transip_api_login_name' environment variable.
         *
         * [--private-key=<private_key>]
         * : The private key for the TransIP API. Defaults to the value of the 'transip_api_private_key' environment variable.
         *
         * --domain=<domain>
         * : The domain name to check.
         *
         * ## EXAMPLES
         *
         *     wp transip check-domain --domain=example.com
         *
         * @param array $args
         * @param array $assoc_args
         */
        public function check_domain($args, $assoc_args)
        {
            $login = $assoc_args['login'] ?? $_ENV['transip_api_login_name'] ?? null;
            $privateKey = $assoc_args['private-key'] ?? $_ENV['transip_api_private_key'] ?? null;
            $domainName = $assoc_args['domain'] ?? null;

            if (!$login) {
                WP_CLI::error("Login is required. Either pass --login or set the 'transip_api_login_name' environment variable.");
            }

            if (!$privateKey) {
                WP_CLI::error("Private key is required. Either pass --private-key or set the 'transip_api_private_key' environment variable.");
            }

            if (!$domainName) {
                WP_CLI::error("Domain name is required. Pass --domain.");
            }

            $transipApi = new TransIP_API($login, $privateKey);

            $status = $transipApi->checkDomain($domainName);
            WP_CLI::log("Domain Status: $status");
        }

        /**
         * Register a new domain name.
         *
         * ## OPTIONS
         *
         * [--login=<login>]
         * : The TransIP login name. Defaults to the value of the 'transip_api_login_name' environment variable.
         *
         * [--private-key=<private_key>]
         * : The private key for the TransIP API. Defaults to the value of the 'transip_api_private_key' environment variable.
         *
         * --domain=<domain>
         * : The domain name to register (without extension).
         *
         * [--extension=<extension>]
         * : The domain extension (e.g. 'nl', 'com'). Defaults to 'nl'.
         *
         * [--test]
         * : Whether to use test mode. In test mode, no actual domain registration will occur.
         *
         * ## EXAMPLES
         *
         *     wp transip register-domain --domain=example
         *     wp transip register-domain --domain=example --extension=com --test
         *
         * @param array $args
         * @param array $assoc_args
         */
        public function register_domain($args, $assoc_args)
        {
            $login = $assoc_args['login'] ?? $_ENV['transip_api_login_name'] ?? null;
            $privateKey = $assoc_args['private-key'] ?? $_ENV['transip_api_private_key'] ?? null;
            $domainName = $assoc_args['domain'] ?? null;
            $extension = $assoc_args['extension'] ?? 'nl';
            $testMode = isset($assoc_args['test']);

            // In test mode with no credentials, we can use the demo token
            if ($testMode && !$login && !$privateKey) {
                $transipApi = new TransIP_API('', '', true, true);
            } else {
                if (!$login) {
                    WP_CLI::error("Login is required. Either pass --login or set the 'transip_api_login_name' environment variable.");
                }

                if (!$privateKey) {
                    WP_CLI::error("Private key is required. Either pass --private-key or set the 'transip_api_private_key' environment variable.");
                }

                $transipApi = new TransIP_API($login, $privateKey, true, $testMode);
            }

            if (!$domainName) {
                WP_CLI::error("Domain name is required. Pass --domain.");
            }

            $result = $transipApi->registerDomain($domainName, $extension);
            
            if ($result['success']) {
                WP_CLI::success($result['message'] . ($testMode ? ' (Test Mode)' : ''));
            } else {
                WP_CLI::error($result['message']);
            }
        }
    }

    WP_CLI::add_command('transip', 'TransIP_CLI_Command');
}
