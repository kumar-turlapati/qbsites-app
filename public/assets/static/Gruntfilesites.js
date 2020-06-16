module.exports = function(grunt) {
  grunt.util.linefeed = "\u000A";
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    concat: {
      options: {
        separator: ';'
      },
      dist: {
        src: [
              '../js/scripts-2.js',
            ],
        dest: '../js/<%= pkg.name %>-2.js'
      }
    },
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      dist: {
        files: {
        '../js/<%= pkg.name %>-2.min.js': ['<%= concat.dist.dest %>']
        }
      }
    },
    cssmin: {
      minify: {
        src: '../css/**/*.css',
        dest: '../css/default.min.css'
      }
    },
    stripCssComments: {
      dist: {
        files: {
        '../dist/default.min.css': '../css/default.min.css'
        },
        options: {
          preserve: false
        }
      }
    }
  });
  
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-stripcomments');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-strip-css-comments'); 

  // grunt.registerTask('default', ['concat', 'uglify', 'cssmin', 'stripCssComments']);
  grunt.registerTask('default', ['concat', 'uglify']);  
};
