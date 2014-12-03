jQuery(document).ready(function(){
  //Callback handler for form submit event
  jQuery('body').prepend('<div id="fb-root"></div>');

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '442675662494471', // App ID
    channelUrl: '//www.theprettyblog.com/channel.html',
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
    xfbml      : true  // parse XFBML
  });
  };

  (function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = '//connect.facebook.net/en_US/all.js';
   ref.parentNode.insertBefore(js, ref);
  }(document));

  window.twttr = (function (d,s,id) {
    var t, js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
    js.src="https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
    return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
  }(document, "script", "twitter-wjs"));
  
  // Setup form validation on the #register-form element
  jQuery("#giveaway-form").validate({
  
    // Specify the validation rules
    rules: {
      giveaway_entrant_name: "required",
      giveaway_entrant_email: {
        required: true,
        email: true
      },
    }, 
    // Specify the validation error messages
    messages: {
      firstname: "Please enter your first name",
      email: "Please enter a valid email address",
    },
    
    submitHandler: function(form) {

      var postData = jQuery(form).serializeArray();
      var formURL = giveawayOptions.ajaxurl;
      jQuery.ajax(
      {
        url : formURL,
        type: "POST",
        data : postData,
        success:function(data, textStatus, jqXHR) 
        {   
          var entry_id = data.data.id;
          var giveaway_id = data.data.giveaway_id;

          jQuery('#giveaway-form').remove();

          jQuery('#giveaway-confirmation').html(jQuery("#confirmationTmpl").render(data));

          try{
            FB.XFBML.parse(); 
          } catch(ex){}

          twttr.widgets.load();

          twttr.events.bind('follow', function (event) {
            giveaway_social_entry(event.type+'-'+event.data.screen_name,entry_id,giveaway_id);
          });

          twttr.events.bind('tweet', function (event) {
            giveaway_social_entry(event.type,entry_id,giveaway_id);
          });
          
          FB.Event.subscribe('edge.create',
            function(href, widget) {
              giveaway_social_entry(jQuery(widget).data('type'),entry_id,giveaway_id);
             }
          );  
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
          //if fails      
        }
      });
    }
  });

});

function giveaway_social_entry(social_label,entry_id,giveaway_id) {
  jQuery.ajax({  
    url: giveawayOptions.ajaxurl, 
    type:'POST',  
    data: "action=add_social&type="+social_label+"&entry_id="+entry_id+'&giveaway_id='+giveaway_id,   
    success: function(data){  
      var container = jQuery('#entry-alert');
      var html = '<h4 class="enter-done-comment-title">You\'ve earned an extra entry</h4>';
      jQuery(container).append(html);
      jQuery(container).show();
    }
  });
}
