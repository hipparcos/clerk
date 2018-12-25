module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    jshint: {
      // Define the files to lint:
      files: ['Gruntfile.js', 'resources/**/*.js', 'tests/**/*.js'],
      // Configure JSHint (documented at http://www.jshint.com/docs/):
      options: {
        // More options here if you want to override JSHint defaults:
        asi: true,
        esversion: 6,
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

  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['jshint']);
};

/* vim: set ts=2 sw=2: */
