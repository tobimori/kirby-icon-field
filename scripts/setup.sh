#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

playground=.playground

pnpm install --frozen-lockfile
composer install --no-interaction

kirby_version="$(php -r '
	$lock = json_decode(file_get_contents("composer.lock"), true);
	foreach (array_merge($lock["packages"], $lock["packages-dev"]) as $package) {
		if ($package["name"] === "getkirby/cms") echo $package["version"];
	}
')"

# Kirby Starterkit at the same Kirby version as the plugin's lock file
if [[ ! -d "$playground" ]]; then
	git -c advice.detachedHead=false clone --quiet --depth 1 --branch "$kirby_version" https://github.com/getkirby/starterkit.git "$playground"
	rm -rf "$playground/.git"
fi

# Kirby pins exact dependency versions, so a security advisory must not block the install
if php -r '
	$require = json_decode(file_get_contents($argv[1]), true)["require"];
	exit(($require["getkirby/cms"] ?? null) === $argv[2] ? 0 : 1);
' "$playground/composer.json" "$kirby_version"; then
	composer install --working-dir="$playground" --no-interaction --no-security-blocking
else
	composer require --working-dir="$playground" --no-interaction --no-security-blocking --with-all-dependencies \
		"getkirby/cms:$kirby_version"
fi

mkdir -p "$playground/site/plugins"
ln -sfn ../../.. "$playground/site/plugins/kirby-icon-field"

if ! grep -q 'scripts/setup.sh' "$playground/site/config/config.php"; then
	cat > "$playground/site/config/config.php" <<'PHP'
<?php

// Created by scripts/setup.sh
return [
	'debug' => true,
	// Portless forwards the public host, Kirby reads it from the proxy headers
	'url' => '*',
	'panel' => ['install' => false],
	'tobimori.icon-field' => [
		// The cache key only covers the field props, so changes to the plugin code would show stale options
		'cache' => false
	]
];
PHP
fi

# Icon field with the Starterkit icons in assets/icons in the default page blueprint
php -r '
	require $argv[1] . "/kirby/bootstrap.php";

	use Kirby\Data\Yaml;

	$file = $argv[1] . "/site/blueprints/pages/default.yml";
	$blueprint = Yaml::read($file);
	if (!isset($blueprint["fields"]["icon"])) {
		$blueprint["fields"]["icon"] = ["type" => "icon", "max" => 1];
		$blueprint["columns"]["main"]["fields"][] = "icon";
		Yaml::write($file, $blueprint);
	}
' "$playground"

# Panel account for people and agents, the credentials stay in the playground
if [[ ! -f "$playground/.panel-credentials" ]]; then
	php -r '
		require $argv[1] . "/kirby/bootstrap.php";

		$kirby = new Kirby\Cms\App(["roots" => ["index" => realpath($argv[1])]]);
		$email = "admin@icon-field.test";
		$password = bin2hex(random_bytes(12));

		$kirby->impersonate("kirby", function () use ($kirby, $email, $password) {
			$kirby->user($email)?->delete();
			Kirby\Cms\User::create([
				"email" => $email,
				"name" => "Admin",
				"role" => "admin",
				"password" => $password
			]);
		});

		file_put_contents($argv[1] . "/.panel-credentials", "PANEL_EMAIL=$email\nPANEL_PASSWORD=$password\n");
	' "$playground"
fi

echo "Playground ready. Start it with: pnpm playground (http://localhost:8000) or pnpm playground:portless"
cat "$playground/.panel-credentials"
