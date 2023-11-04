<?php
declare(strict_types=1);

$posts = [
    (object)[
        "title" => "Hello World",
        "status" => "published",
    ],
    (object)[
        "title" => "Hello World 2",
        "status" => "draft",
    ],
    (object)[
        "title" => "Hello World 3",
        "status" => "published",
    ],
];

$input = [
    "pinned_post" => 0,
    "likes" => [
        1,
        2,
    ],
];

$v = new Validator($input);
try {
    $v->isArrayOfShape([
        "pinned_post" => fn(Validator $v) => $v->isInt("Pinned post ID must be an integer")
            ->isOneOf(array_keys($posts), "Pinned post ID must be a valid post ID")
            ->transform(fn($v) => $posts[$v] ?? null)
            ->validate(function ($post) {
                if ($post->status !== "published") {
                    throw new ValidatorException("Pinned post must be published");
                }
            }),
        "likes" => fn(Validator $v) => $v->isUnique("Likes must not contain duplicate values")
            ->isArray(fn(Validator $v) => $v->isInt("Liked post ID must be an integer")
                ->isOneOf(array_keys($posts), "Liked post ID must be a valid post ID")
                ->transform(fn($v) => $posts[$v] ?? null)
            )
    ]);
} catch (ValidatorException $exception) {
    die("Invalid: " . $exception->getMessage());
}

$validated = $v->get();
var_dump($validated);
