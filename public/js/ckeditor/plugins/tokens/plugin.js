CKEDITOR.plugins.add( 'tokens',
{   
   requires : ['richcombo'], //, 'styles' ],
   init : function( editor )
   {
      var config = editor.config,
         lang = editor.lang.format;

      // Gets the list of tags from the settings.
      var tags = []; //new Array();
      //this.add('value', 'drop_text', 'drop_label');

      // tags[0]=["[contact_name]", "Name", "Name"];
      // tags[1]=["[contact_email]", "email", "email"];
      // tags[2]=["[contact_user_name]", "User name", "User name"];

      tags[0]=["$name", "Name", "Name"];
      tags[1]=["$father", "Father", "Father's Name"];
      tags[2]=["$mother", "Mother", "Mother's Name"];
      tags[3]=["$village", "Village", "Village"];
      tags[4]=["$post", "Post", "Post Office"];
      tags[5]=["$upazilla", "Upazilla", "Upazilla"];
      tags[6]=["$district", "District", "District"];
      tags[7]=["$session", "Session", "Session (Ex- 2007-2008)"];     
      tags[8]=["$study_year", "Study-Year", "Study Year (Ex- 1st )"];
      tags[9]=["$exam_year", "Exam-Year", "Examination Year (Ex- 2007)"];
      tags[10]=["$group", "Group", "Group (Ex- Commerce)"];
      tags[11]=["$subject", "Subject", "Subject (Ex- Economics)"];
      tags[12]=["$result", "Result", "Result (CGPA/Division)"];
      tags[13]=["$nu_roll", "NU-Roll", "National University Roll No."];
      tags[14]=["$class_roll", "Class-Roll", "Class Roll No."];
      tags[15]=["$registration_no", "Regi. No.", "Registration. No."];
      tags[16]=["$class", "Class", "Class (Ex- Honours 1st Year)"];
      tags[17]=["$level", "Level", "Study Level (Ex- Honours)"];
      tags[18]=["$date", "Date", "Date"];

     
      
      // Create style objects for all defined styles.

      editor.ui.addRichCombo( 'tokens',
         {
            label : "Variables",
            title :"Variables",
            voiceLabel : "Insert tokens",
            className : 'cke_format',
            multiSelect : false,

            panel :
            {
               css : [ config.contentsCss, CKEDITOR.getUrl( editor.skinPath + 'editor.css' ) ],
               voiceLabel : lang.panelVoiceLabel
            },

            init : function()
            {
               //this.startGroup( "Tokens" );
               this.startGroup( "" );
               //this.add('value', 'drop_text', 'drop_label');
               for (var this_tag in tags){
                  this.add(tags[this_tag][0], tags[this_tag][1], tags[this_tag][2]);
               }
            },

            onClick : function( value )
            {         
               editor.focus();
               editor.fire( 'saveSnapshot' );
               editor.insertHtml(value);
               editor.fire( 'saveSnapshot' );
            }
         });
   }
});


