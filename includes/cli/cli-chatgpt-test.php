<?php
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('chatgpt', function ($args, $assoc_args) {
        $chatGPTApiKey = $_ENV['chatgpt_api_key'] ?? null;

        if (!$chatGPTApiKey) {
            WP_CLI::error('ChatGPT API key is missing. Set CHATGPT_API_KEY in your .env file.');
        }

        $chatGPTApi = new ChatGPT_API($chatGPTApiKey);

        if (empty($args)) {
            WP_CLI::error('Please provide a subcommand. Available: generate-chat, suggest-domain, suggest-ai-domain, test-suggest-domain');
        }

        $subcommand = $args[0];

        switch ($subcommand) {
            case 'generate-chat':
                $message = $assoc_args['message'] ?? 'Hello!';
                $userMessages = [['role' => 'user', 'content' => $message]];
                $response = $chatGPTApi->generateChat($userMessages);
                WP_CLI::success("ChatGPT Response: $response");
                break;

            case 'suggest-domain':
                $company = $assoc_args['company'] ?? 'Default Company';
                $domain = $chatGPTApi->suggestDomainNameWithAI($company);
                WP_CLI::success("Suggested AI Domain: $domain");
                break;

            case 'test-suggest-domain':
                $companyNames = [
                    'Janssen Schilderwerken', 'Pieters Timmerbedrijf', 'De Groot Elektricien', 'Van Dijk Stucadoors',
                    'Klaassen Bouwservice', 'Bouwbedrijf De Vries', 'Timmerbedrijf Jansen', 'Schilderwerken Smits',
                    'Elektricien Mulder', 'Stucadoorsbedrijf Vos', 'Bouwservice Van Dam', 'Timmerwerken De Boer',
                    'Schilderbedrijf Hendriks', 'Stucadoorsbedrijf Vermeer', 'Van Leeuwen Elektrotechniek',
                    'Bouwbedrijf Van den Berg', 'Timmerbedrijf Hoekstra', 'Schilderwerken Dekker', 'Elektricien De Jong',
                    'Stucadoorsbedrijf Smit', 'Bouwservice Brouwer', 'Timmerwerken Meijer', 'Schilderbedrijf Bakker',
                    'Stucadoorsbedrijf Kuipers', 'Elektrotechniek Van der Linden', 'Timmerbedrijf Koster',
                    'Schilderwerken De Lange', 'Elektricien De Vries', 'Stucadoorsbedrijf Mulder',
                    'Bouwbedrijf Van der Meer', 'Timmerwerken De Groot', 'Schilderbedrijf Janssen',
                    'Stucadoorsbedrijf Molenaar', 'Van der Berg Elektrotechniek', 'Bouwservice Van der Heijden',
                    'Timmerbedrijf Dijkstra', 'Schilderwerken De Wit', 'Elektricien Jansen', 'Stucadoorsbedrijf Postma',
                    'Bouwbedrijf De Haan', 'Timmerwerken Kramer', 'Schilderbedrijf Visser', 'Stucadoorsbedrijf De Ruiter',
                    'Van der Pol Elektrotechniek', 'Bouwservice Hendriks', 'Timmerbedrijf Van der Veen',
                    'Schilderwerken Van Loon', 'Elektricien Brouwer', 'Stucadoorsbedrijf Peters', 'Bouwbedrijf Verhoeven',
                    'Timmerwerken Jacobs', 'Schilderbedrijf De Bruijn', 'Stucadoorsbedrijf Hermans',
                    'Van der Wal Elektrotechniek', 'Bouwservice Peters', 'Timmerbedrijf Willems',
                    'Schilderwerken De Ridder', 'Elektricien Van der Ven', 'Stucadoorsbedrijf Maas',
                    'Bouwbedrijf Van den Broek', 'Timmerwerken De Vos', 'Schilderbedrijf Van Beek',
                    'Stucadoorsbedrijf De Koning', 'Elektrotechniek Sanders', 'Timmerbedrijf Van der Meulen',
                    'Schilderwerken Van Dam', 'Elektricien Vermeulen', 'Stucadoorsbedrijf Jansen',
                    'Bouwbedrijf De Graaf', 'Timmerwerken De Leeuw', 'Schilderbedrijf Scholten',
                    'Stucadoorsbedrijf Van Hout', 'Van der Heijden Elektrotechniek', 'Bouwservice De Boer',
                    'Timmerbedrijf Bakker', 'Schilderwerken De Rooij', 'Elektricien Van der Kolk',
                    'Stucadoorsbedrijf Dijkstra', 'Bouwbedrijf De Zwart', 'Timmerwerken Veenstra',
                    'Schilderbedrijf Ter Horst', 'Stucadoorsbedrijf De Haas', 'Van den Berg Elektrotechniek',
                    'Bouwservice Mulder', 'Timmerbedrijf De Ruiter', 'Schilderwerken Van Vliet',
                    'Elektricien Smit', 'Stucadoorsbedrijf Van Dijk', 'Bouwbedrijf Van Dijk',
                    'Timmerwerken De Bruin', 'Schilderbedrijf De Haan', 'Stucadoorsbedrijf Kramer',
                    'Van der Steen Elektrotechniek', 'Bouwservice De Ruiter', 'Timmerbedrijf Dekker',
                    'Schilderwerken Van den Berg', 'Elektricien Hendriks', 'Stucadoorsbedrijf Vos'
                ];

                foreach ($companyNames as $companyName) {
                    $output = shell_exec("wp chatgpt suggest-domain --company=\"$companyName\"");
                    WP_CLI::line("Company: $companyName => Domain: " . trim($output));
                }
                WP_CLI::success('Test completed for 100 companies.');
                break;

            default:
                WP_CLI::error('Unknown subcommand. Available: generate-chat, suggest-domain, test-suggest-domain');
        }
    });
}
