<?php
function getMovieSummary($title, $synopsis) {
    $cacheFile = 'cache/summaries.json';

    if (!file_exists('cache')) {  // cache file exist check
        mkdir('cache', 0777, true);
    }

    $cache = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];   // load existing cache

    if (isset($cache[$title])) {    // is summary already cached?
        return $cache[$title];
    }

    $apiKey = "OPENAI-KEY-HERE";
    $apiUrl = "https://api.openai.com/v1/chat/completions";

    $prompt = "A new movie titled '$title' has just been released, and its synopsis is as follows: $synopsis. Please provide a spoiler-free summary of the previous films in this series, on the original source of the universe the movie takes place in, or relevant background information that will help viewers understand the story. Your summary should relate to the themes or elements that the upcoming movie may explore, catering to those attending the new release. Focus solely on essential background details while avoiding any spoilers. Limit your response to one paragraph containing 4-6 sentences. If the movie is adapted from a book, game or other source, feel free to provide a summary based on the original material. If you are unfamiliar with the movie or franchise, please respond with 'Summary not available.'";

    $data = [
        "model" => "gpt-4o-mini",
        "messages" => [
            ["role" => "system", "content" => "You are a movie guide providing spoiler-free summaries."],
            ["role" => "user", "content" => $prompt]
        ],
        "temperature" => 0.7
    ];

    $headers = [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    $summary = $responseData['choices'][0]['message']['content'] ?? "No summary available.";    // no summary available, if error

    $cache[$title] = $summary;
    file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT));  // save response to cache

    return $summary;
}
?>