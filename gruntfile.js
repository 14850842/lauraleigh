module.exports = function(grunt) {
  grunt.initConfig({
    less: {
      development: {
        options: {
          compress: true,
          yuicompress: true,
          optimization: 2
        },
        files: {
          // target.css file: source.less file
          "wp-content/themes/lauraleigh/library/css/style.css": "wp-content/themes/lauraleigh/library/less/style.less"
        }
      }
    },
    'ftp-deploy': {
      build: {
        auth: {
          host: 'clients.plusplusminus.co.za',
          port: 21,
          authKey: 'key1'
        },
        src: 'wp-content/themes/lauraleigh/library/css/',
        dest: 'public_html/lauraleigh/wp-content/themes/lauraleigh/library/css/',
        exclusions: ['wp-content/themes/lauraleigh/library/css/.DS_Store']
      }
    },
    watch: {
      styles: {
        files: ['"wp-content/themes/lauraleigh/library/less/*.less',"wp-content/themes/lauraleigh/library/less/**/*.less"], // which files to watch
        tasks: ['less'],
        options: {
          nospawn: true,
        }
      }
    },
  });

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-ftp-deploy');


  grunt.registerTask('default', ['watch']);
};