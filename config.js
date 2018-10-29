let path = require( 'path' );

let $_json               = {};
$_json[ 'project_name' ] = 'WPOnion';
$_json[ 'scss' ]         = {
	"src/scss/wponion-base.scss": {
		sourcemap: false,
		concat: 'wponion-base.css',
		dist: 'assets/css/'
	},
	"src/scss/wponion-plugins.scss": {
		sourcemap: false,
		concat: 'wponion-plugins.css',
		dist: 'assets/css/'
	},
	"src/scss/selectize.scss": {
		sourcemap: false,
		concat: 'selectize.css',
		dist: 'assets/plugins/selectize/'
	},

	/**
	 * Plugins
	 */
	"src/vendors/flatpickr/style.scss": {
		sourcemap: false,
		concat: "style.css",
		dist: 'assets/plugins/flatpickr/'
	},
	"src/vendors/colorpicker/cs-colorpicker.css": {
		concat: 'cs-colorpicker.css',
		dist: 'assets/plugins/colorpicker',
		scss: false,
		sourcemap: false,
		watch: [ 'src/js/wponion-plugins.js' ],
	}
};
$_json[ 'js' ]           = {
	"src/js/wponion-plugins.js": {
		dist: "assets/js",
		webpack: false,
		webpack_dev: false,
		babel: false,
		babel_dev: false,
		combine_files: true,
		combine_files_dev: true,
		concat: "wponion-plugins.js",
	},
	"src/js/wponion-core.js": {
		dist: "assets/js",
		watch: [ 'src/js/core/*', 'src/js/fields/*', 'src/js/helpers/*', 'src/js/modules/*' ],
		webpack: true,
		babel: false,
		babel_dev: false,
		uglify_dev: false,
		uglify: true,
		combine_files: true,
		concat: "wponion-core.js",
	},
	"src/js/themes/wp-theme.js": {
		dist: "templates/wp/assets/",
		babel: false, babel_dev: false, uglify: true, combine_files: true, concat: 'wponion-wp-theme.js'
	},
	"src/js/themes/modern-theme.js": {
		dist: "templates/modern/assets/",
		babel: false, babel_dev: false, uglify: true, combine_files: true, concat: 'wponion-modern-theme.js'
	},

	/**
	 * Plugins.
	 */
	"src/vendors/flatpickr/script.js": {
		dist: "assets/plugins/flatpickr",
		webpack: false,
		webpack_dev: false,
		babel: false,
		babel_dev: false,
		combine_files: true,
		combine_files_dev: true,
		concat: "script.js",
	},
	"src/vendors/colorpicker/wp-color-picker-alpha.js": {
		babel: false,
		webpack: false,
		uglify: true,
		combine_files: false,
		dist: 'assets/plugins/colorpicker/',
		concat: 'wp-color-picker-alpha.js',
		watch: [ './src/js/wponion-plugins.js' ],
	}
};

/**
 * Settings any feature to false will not trigger for any files untill its
 * overridden in file config.
 * js:{
 *     "your_file_source":{
 *         scss:true,
 *         dist:"your_file_dist",
 *     }
 * }
 */
$_json[ 'status' ] = {
	scss: true,
	autoprefixer: true,
	sourcemap: true,
	webpack: true,
	babel: false,
	parcel: false,
	rollup: false,
	minify: true,
	uglify: true,
	combine_files: true,
	concat: true,
};
$_json[ 'default_config' ] = {
	/**
	 * Production Configs.
	 */
	combine_files: {
		append: 'wponion-append',
		prepend: 'wponion-prepend',
		inline: 'wponion-inline',
	},
	minify: {
		args: {},
		callback: false
	},
	concat: {},
	scss: {
		outputStyle: "expanded"
	},
	sourcemap: "../maps",
	autoprefixer: {
		browsers: [ "last 2 version", "safari 5", "ie 8", "ie 9", "opera 12.1", "ios 6", "android 4" ],
		cascade: false
	},
	webpack: {
		mode: "production",
		output: {
			filename: '[name].js',
		},
		target: "node",
		module: {
			rules: [
				{
					test: /\.js$/,
					loader: 'babel-loader',
					options: {
						presets: [ 'es2015' ]
					}
				}
			]
		},
	},
	parcel: false,
	uglify: true,
	babel: {
		presets: [ '@babel/env' ],
	},

	/**
	 * Development Config.
	 */
	webpack_dev: {
		devtool: "inline-source-map",
		mode: "development",
		target: "node",
		output: {
			filename: '[name].js',
		},
		module: {
			rules: [
				{
					test: /\.js$/,
					loader: 'babel-loader',
					options: {
						presets: [ 'es2015' ]
					}
				}
			]
		},
	},
	uglify_dev: false,
};
module.exports             = $_json;
