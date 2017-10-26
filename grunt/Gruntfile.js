module.exports = function(grunt) {
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		banner: "/*\n<%= pkg.name %> v<%= pkg.version %>\nAuthor: <%= pkg.author %>\nDate: <%= grunt.template.today(\"yyyy-mm-dd\") %>\n*/\n\n",

		// Typescript compilation
		typescript: {
			base: {
				src: ['../assets/src/js/**/*.ts'],
				dest: '../assets/src/js/',
				options: {
					target: 'ES5',
				}
			}
		},

		// JS concatenation and minification
		uglify: {
			options: {
				banner: '<%= banner %>'
			},
			all: {
				src: [
					'../assets/lib/jquery/jquery-3.2.1.min.js',
					'../assets/lib/jquery-localscroll/jquery.localScroll.min.js',
					'../assets/lib/jquery-scrollto/jquery.scrollTo.min.js',
					'../assets/lib/waypoints/waypoints.min.js',
					'../assets/src/js/**/*.js',
				],
				dest: '../assets/dist/js/<%= pkg.name %>.min.js'
			}
		},

		// LESS 2 CSS conversion
		less: {
			options: {
				banner: '<%= banner %>',
			},
			all: {
				src: [
					'../assets/src/css/normalize.less',
					'../assets/src/css/*.less'
				],
				dest: '../assets/dist/css/<%= pkg.name %>.css'
			}
		},

		// CSS minification
		cssmin: {
			options: {
				banner: '<%= banner %>',
			},
			all: {
				src: '<%= less.all.dest %>',
				dest: '../assets/dist/css/<%= pkg.name %>.min.css'
			}
		},

		// Watcher
		watch: {
			options: {
				livereload: true
			},
			gruntfile: {
				files: ['Gruntfile.js','package.json'],
				tasks: ['default']
			},
			app: {
				files: [
					'../core/**/*',
					'../design/**/*',
					'../controllers/**/*',
					'../views/**/*'
				],
				tasks: []
			},
			typescript: {
				files: ['<%= typescript.base.src %>'],
				tasks: ['typescript']
			},
			js: {
				files: ['<%= uglify.all.src %>'],
				tasks: ['uglify']
			},
			less: {
				files: ['<%= less.all.src %>'],
				tasks: ['less']
			},
			css: {
				files: ['<%= less.all.dest %>'],
				tasks: ['cssmin']
			}
		}
	});

	grunt.loadNpmTasks('grunt-typescript');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Tasks configuration.
	grunt.registerTask('default', ['typescript','uglify','less','cssmin']);
};
