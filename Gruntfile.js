module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    browserify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      js: {
        src: [
          'node_modules/@fortawesome/fontawesome-free/js/all.js',
          'resources/js/*.js',
        ],
        dest: 'public/js/<%= pkg.name %>.js',
      },
    },
    concat: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      // js: {
      //   src: [
      //     'node_modules/lodash/lodash.js',
      //     'resources/js/*.js',
      //   ],
      //   dest: 'public/js/<%= pkg.name %>.js'
      // },
      css: {
        src: [
          'node_modules/bulma/css/bulma.css',
          'node_modules/@fortawesome/fontawesome-free/css/all.css',
          'resources/css/*.css'
        ],
        dest: 'public/css/<%= pkg.name %>.css'
      },
    },
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      dist: {
        files: {
          'public/js/<%= pkg.name %>.min.js': ['<%= browserify.js.dest %>']
        }
      }
    },
    cssmin: {
      target: {
        files: {
          'public/css/<%= pkg.name %>.min.css': ['<%= concat.css.dest %>'],
        }
      }
    },
    jshint: {
      // Define the files to lint:
      files: ['Gruntfile.js', 'resources/**/*.js', 'tests/**/*.js'],
      // Configure JSHint (documented at http://www.jshint.com/docs/):
      options: {
        // More options here if you want to override JSHint defaults:
        globals: {
          jQuery: true,
          console: true,
          module: true
        }
      }
    },
    watch: {
      files: ['<%= jshint.files %>'],
      tasks: ['jshint']
    },
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-browserify');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['jshint', 'browserify', 'concat', 'uglify']);
};

/* vim: set ts=2 sw=2: */
