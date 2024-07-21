<?php
// anthropic.php
//'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';

class Anthropic {
    private $api_key;
    private $api_url = 'https://api.anthropic.com/v1/messages';

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function createMessage($model, $max_tokens, $messages, $additional_params = []) {
        $data = array_merge([
            'model' => $model,
            'max_tokens' => $max_tokens,
            'messages' => $messages
        ], $additional_params);

        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => [
                    'Content-Type: application/json',
                    'x-api-key: ' . $this->api_key,
                    'anthropic-version: 2023-06-01'
                ],
                'content' => json_encode($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($this->api_url, false, $context);

        if ($result === FALSE) {
            throw new Exception("Error occurred while calling Anthropic API");
        }

        return json_decode($result, true);
    }
}
?>