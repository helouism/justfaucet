<?php

namespace Config;

use Michalsn\Minifier\Config\Minifier as BaseMinifier;
use Michalsn\Minifier\Adapters\Css\MinifyAdapter as CssMinifyAdapter;
use Michalsn\Minifier\Adapters\Js\MinifyAdapter as JsMinifyAdapter;

class Minifier extends BaseMinifier
{
  // --------------------------------------------------------------------
  // Enable minify class
  // --------------------------------------------------------------------
  // Use this variable to turn on and off minification of the assets.
  // This can be useful during app development - for easy debugging.

  public bool $minify = true;

  // --------------------------------------------------------------------
  // Base URL for assets
  // --------------------------------------------------------------------
  // Use this variable when you want to set absolute path to the asset
  // files. If no other URLs are set, like $baseJsUrl or $baseCssUrl
  // then values set to $dirJS and $dirCss will be added to the final URL.
  //
  // Example values:
  //      https://mydomain.com
  //      https://static.mydomain.com

  public ?string $baseUrl = 'http://localhost:8080/';

  // --------------------------------------------------------------------
  // Base JS URL for assets
  // --------------------------------------------------------------------
  // Use this variable when your JS assets are served from subdomain.
  // Bear in mind that in this case variable $dirJs won't be added
  // to the URL.
  //
  // Example value:
  //      https://js.mydomain.com

  public ?string $baseJsUrl = null;

  // --------------------------------------------------------------------
  // Base CSS URL for assets
  // --------------------------------------------------------------------
  // Use this variable when your CSS assets are served from subdomain.
  // Bear in mind that in this case variable $dirCSS won't be added
  // to the URL.
  //
  // Example value:
  //      https://css.mydomain.com

  public ?string $baseCssUrl = null;

  // --------------------------------------------------------------------
  // JS adapter
  // --------------------------------------------------------------------

  public string $adapterJs = JsMinifyAdapter::class;

  // --------------------------------------------------------------------
  // CSS adapter
  // --------------------------------------------------------------------

  public string $adapterCss = CssMinifyAdapter::class;

  // --------------------------------------------------------------------
  // JS assets directory
  // --------------------------------------------------------------------

  public string $dirJs = './assets/main/js';

  // --------------------------------------------------------------------
  // CSS assets directory
  // --------------------------------------------------------------------

  public string $dirCss = './assets/main/css';

  // --------------------------------------------------------------------
  // JS minified assets directory
  // --------------------------------------------------------------------

  public ?string $dirMinJs = './assets/main/js';

  // --------------------------------------------------------------------
  // CSS minified assets directory
  // --------------------------------------------------------------------

  public ?string $dirMinCss = './assets/main/css';

  // --------------------------------------------------------------------
  // Version assets directory
  // --------------------------------------------------------------------

  public string $dirVersion = './assets';

  // --------------------------------------------------------------------
  // JS tag
  // --------------------------------------------------------------------

  public string $tagJs = '<script type="text/javascript" src="%s"></script>';

  // --------------------------------------------------------------------
  // CSS tag
  // --------------------------------------------------------------------

  public string $tagCss = '<link rel="stylesheet" href="%s">';

  // --------------------------------------------------------------------
  // Return type
  // --------------------------------------------------------------------
  // Determines how the files will be returned. The default value is
  // 'html' and it uses the $tagJs and $tagCss variables. Using 'array'
  // will return the php array and 'json' type will return a json string.
  //
  // Available types:
  //      'html', 'array', 'json'

  public string $returnType = 'html';

  // --------------------------------------------------------------------
  // Enable auto deploy on change
  // --------------------------------------------------------------------
  // Use this variable to automatically deploy when there are any
  // changes in assets files.

  public bool $autoDeployOnChange = false;

  // --------------------------------------------------------------------
  // JS files config
  // --------------------------------------------------------------------
  // This array defines files to minify.
  //
  // Example array:
  //      'all.min.js' => [
  //          'jquery-3.2.1.min.js', 'bootstrap-3.3.7.min.js', 'main.js',
  //      ],

  public array $js = [
    'main.min.js' => [
      'script.js',

    ],
  ];

  // --------------------------------------------------------------------
  // CSS files config
  // --------------------------------------------------------------------
  // This array defines files to minify.
  //
  // Example array:
  //      'all.min.css' => [
  //          'bootstrap-3.3.7.min.css', 'font-awesome-4.7.0.min.css', 'main.css',
  //      ],

  public array $css = [

  ];
}
