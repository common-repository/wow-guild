function getGuild(guild,realm,realmType,showNo,i,baseURL,start,linkback) {
  var myurl = baseURL + "/wp-content/plugins/wow-guild/js/get_guild.php";
  var modurl = "guild=" + guild.replace(" ", "+") + "&realm=" + realm.replace(" ","+") + "&realmType=" + realmType + "&showNo=" + showNo + "&start=" + start + "&id=" + i + "&linkback=" + linkback + "&baseURL=" + baseURL;
  this.timer = setTimeout(function () {
    jQuery.ajax({
      url: myurl,
      data: modurl,
      type: "GET",
      success: function(msg) {
        jQuery("#guild-" + i).fadeOut('slow', function () {
          jQuery("#guild-" + i).html(msg);
          jQuery("#guild-" + i).fadeIn('slow');
          setClicks(baseURL);
        });
      },
      error: function(msg) {
        jQuery("#guild-" + i).fadeOut('slow', function () {
          jQuery("#guild-" + i).html("<h3>The Armory is Unavailable at this time.</h3>");
          jQuery("#guild-" + i).fadeIn('slow');
        });
      }
    });
  }, 200);
  jQuery.post("http://timsworld.nfshost.com/test/plugin_tracking.php", { action: escape("trackback"), url: escape(baseURL), plugin: escape("guild") } );
}

function setClicks(baseURL) {
  jQuery('a.guild_back, a.guild_next').click(function(e) {
    e.preventDefault();
    var t = jQuery(this);        
    getGuild(t.children('.guild_gname').val(), t.children('.guild_realm').val(), t.children('.guild_realmtype').val(), t.children('.guild_showno').val(), t.children('.guild_no').val(), baseURL, t.children('.guild_start').val(),t.children('.guild_link').val());
  });
}