CommentPress.theme={},CommentPress.theme.settings=new function(){jQuery.noConflict();this.init=function(){},this.dom_ready=function(){}},CommentPress.theme.DOM=new function(){var e=this,t=jQuery.noConflict();this.init=function(){e.head(),t("html").addClass("js")},this.dom_ready=function(){},this.head=function(){var e,t;e="",t=70,document.getElementById&&(e+='<style type="text/css" media="screen">',"0"==cp_is_mobile&&"0"==cp_textblock_meta&&(e+="#content .textblock span.para_marker, #content .textblock span.commenticonbox { display: none; } ",e+=".content .textblock span.para_marker, .content .textblock span.commenticonbox { display: none; } "),e+="ul.all_comments_listing div.item_body { display: none; } ","y"==CommentPress.settings.DOM.get_wp_adminbar()&&(e+="body.admin-bar #header, #header { top: "+CommentPress.settings.DOM.get_wp_adminbar_height()+"px; } ",e+="body.admin-bar #sidebar, #sidebar, body.admin-bar #navigation, #navigation { top: "+(CommentPress.settings.DOM.get_wp_adminbar_height()+t)+"px; } ","32"==CommentPress.settings.DOM.get_wp_adminbar_height()&&(e+="@media screen and ( max-width: 782px ) { body.admin-bar #header, #header { top: "+CommentPress.settings.DOM.get_wp_adminbar_expanded()+"px; }body.admin-bar #sidebar, #sidebar, body.admin-bar #navigation, #navigation { top: "+(CommentPress.settings.DOM.get_wp_adminbar_expanded()+t)+"px; } } ")),"0"==cp_show_subpages&&(e+="#toc_sidebar .sidebar_contents_wrapper ul li ul { display: none; } ",e+="#toc_sidebar .sidebar_contents_wrapper ul li.current_page_ancestor > ul { display: block; } "),"0"==cp_special_page&&(e+="#respond { display: none; } "),e+="#sidebar .paragraph_wrapper { display: none; } ",e+="#navigation .paragraph_wrapper { display: none; } ",e+="#sidebar .paragraph_wrapper.start_open { display: block; } ",e+="#navigation .paragraph_wrapper.start_open { display: block; } ",e+=".commentpress_page #navigation .paragraph_wrapper.special_pages_wrapper { display: block; } ",e+="#original .post, #literal .post { display: none; } ",e+="</style>"),document.write(e)}},CommentPress.theme.header=new function(){var e=jQuery.noConflict();this.init=function(){},this.dom_ready=function(){},this.get_offset=function(){var t;return t=0-e("#header").height()-e("#sidebar_tabs").height(),"y"==CommentPress.settings.DOM.get_wp_adminbar()&&(t-=CommentPress.settings.DOM.get_wp_adminbar_height()),t}},CommentPress.theme.navigation=new function(){var e=this,t=jQuery.noConflict();this.init=function(){},this.dom_ready=function(){e.menu()},this.menu=function(){t("#toc_sidebar").on("click","ul#nav li a",function(e){var n;n=t(this).parent().find("ul"),n.length>0&&(t(this).next("ul").slideToggle(),e.preventDefault())})}},CommentPress.theme.content=new function(){var e=this,t=jQuery.noConflict();this.init=function(){},this.dom_ready=function(){e.tabs()},this.tabs=function(){var e,n;e=t("#page_wrapper").css("min-height"),n=t("#page_wrapper").css("padding-bottom"),t("#literal .post").css("display","none"),t("#original .post").css("display","none"),CommentPress.common.content.workflow_tabs(e,n)}},CommentPress.theme.sidebars=new function(){var e=this,t=jQuery.noConflict();this.init=function(){},this.dom_ready=function(){e.enable_buttons(),e.set_height()},this.set_height=function(){var e,n,o,s,a,i,r,m,c;e=t(window).height(),n=t("#header").height(),o=t("#switcher").height(),s=t("#toc_sidebar > .sidebar_header").height(),a="y"==CommentPress.settings.DOM.get_wp_adminbar()?t("#wpadminbar").height():0,i=e-(n+s+a),r=t("#switcher").css("display"),"block"===r&&(i-=o),t("#toc_sidebar .sidebar_contents_wrapper").css("height",i+"px"),m=t("#sidebar_tabs").height(),c=e-(n+m+a),"block"===r&&(c-=o),t("#sidebar .sidebar_contents_wrapper").css("height",c+"px")},this.activate_sidebar=function(e){"comments"==e&&(!t("body").hasClass("active-sidebar")||t("body").hasClass("active-nav"))&&CommentPress.theme.sidebars.show_discuss();var n;n=t("#"+e+"_sidebar").css("z-index"),"2001"==n&&(t(".sidebar_container").css("z-index","2001"),t("#sidebar_tabs h2 a").removeClass("active-tab"),t("#"+e+"_sidebar").css("z-index","2010"),t("#sidebar_tabs #"+e+"_header h2 a").addClass("active-tab"))},this.enable_buttons=function(){t(".navigation-button").click(function(t){t.preventDefault(),e.show_nav()}),t(".content-button").click(function(t){t.preventDefault(),e.show_content()}),t(".sidebar-button").click(function(t){t.preventDefault(),e.show_discuss()})},this.show_nav=function(){t("body").toggleClass("active-nav").removeClass("active-sidebar"),t(".sidebar-button,.content-button").removeClass("active-button"),t(".navigation-button").toggleClass("active-button")},this.show_content=function(){t("body").removeClass("active-sidebar").removeClass("active-nav"),t(".navigation-button,.sidebar-button").removeClass("active-button"),t(".content-button").toggleClass("active-button")},this.show_discuss=function(){t("body").toggleClass("active-sidebar").removeClass("active-nav"),t(".navigation-button,.content-button").removeClass("active-button"),t(".sidebar-button").toggleClass("active-button")}},CommentPress.theme.viewport=new function(){var e=this,t=jQuery.noConflict();this.init=function(){},this.dom_ready=function(){e.track_resize(),e.track_scrolling()},this.track_resize=function(){t(window).resize(function(){CommentPress.theme.sidebars.set_height()})},this.track_scrolling=function(){t(window).scroll(function(){var e,n,o,s,a,i,r,m,c,p,d,_,l;if(p=t("#header"),position=p.css("position"),"absolute"==position)if(n=p.height(),d=p.position(),_=window.pageYOffset-(d.top+n),parseInt(_)>0){if(l=t.px_to_num(t("html body #content_container #sidebar").css("top")),"0"==l)return;t("html body #content_container #sidebar,html body #content_container #navigation").css("top","0"),e=t(window).height(),o=t("#switcher").height(),s=t("#toc_sidebar > .sidebar_header").height(),i=e-s,r=t("#switcher").css("display"),"block"===r&&(i-=o),t("#toc_sidebar .sidebar_contents_wrapper").css("height",i+"px"),m=t("#sidebar_tabs").height(),c=e-m,"block"===r&&(c-=o),t("#sidebar .sidebar_contents_wrapper").css("height",c+"px")}else{if(a="y"==CommentPress.settings.DOM.get_wp_adminbar()?t("#wpadminbar").height():0,l=t.px_to_num(t("html body #content_container #sidebar").css("top")),l==n+a)return;t("html body #content_container #sidebar,html body #content_container #navigation").css("top",n+a+"px"),CommentPress.theme.sidebars.set_height()}})},this.scroll_to_top=function(e,n){if("undefined"!=typeof e){var o;("0"==cp_is_mobile||"1"==cp_is_tablet)&&(0==e&&(o=t(".comments_container").prop("id"),"undefined"!=typeof o&&(target_id=o.split("-")[1],e=t("#post-"+target_id))),t(window).stop(!0).scrollTo(e,{duration:1.5*n,axis:"y",offset:CommentPress.theme.header.get_offset()}))}},this.on_load_scroll_to_anchor=function(){var e,n,o,s,a,i,r,m,c,p,d;if(e="",n=document.location.toString(),n.match("#comment-")){if(CommentPress.theme.sidebars.activate_sidebar("comments"),o=n.split("#comment-")[1],s=t("#comment-"+o).parents("div.paragraph_wrapper").map(function(){return this}),s.length>0)return a=t(s[0]),"y"==cp_comments_open&&(e=a.prop("id").split("-")[1],i=t("#para_wrapper-"+e+" .reply_to_para").prop("id"),r=i.split("-")[1],m=t("#comment_post_ID").prop("value"),"1"==cp_tinymce?""!==t("#comment-"+o+" > .reply").text()&&(cp_tinymce="0",addComment.moveForm("comment-"+o,o,"respond",m,e),cp_tinymce="1"):addComment.moveForm("comment-"+o,o,"respond",m,e)),a.show(),CommentPress.common.comments.scroll_comments(t("#comment-"+o),1,"flash"),void(""!==e?(c=t("#textblock-"+e),t.highlight_para(c),CommentPress.common.content.scroll_page(c)):(CommentPress.settings.page.get_highlight()||CommentPress.theme.viewport.scroll_to_top(0,cp_scroll_speed),CommentPress.settings.page.toggle_highlight()))}else t("span.para_marker > a").each(function(e){var o,s,a,i,r;return o=t(this).prop("id"),n.match("#"+o)||n.match("#para_heading-"+o)?("y"==cp_comments_open&&(s=t("#para_wrapper-"+o+" .reply_to_para").prop("id"),a=s.split("-")[1],i=t("#comment_post_ID").prop("value"),addComment.moveFormToPara(a,o,i)),t("#para_heading-"+o).next("div.paragraph_wrapper").show(),CommentPress.common.comments.scroll_comments(t("#para_heading-"+o),1),r=t("#textblock-"+o),t.highlight_para(r),void CommentPress.common.content.scroll_page(r)):void 0});if(n.match("#respond"))return void t("h3#para_heading- a.comment_block_permalink").click();if(n.match("#")){if(p=n.split("#")[1],"edit=true"==p)return;if("fee-edit-link"==p)return;return d=t("#"+p),void(d.length&&CommentPress.common.content.scroll_page(d))}},this.align_content=function(e,n){if("none"!=n){(!t("body").hasClass("active-sidebar")||t("body").hasClass("active-nav"))&&CommentPress.theme.sidebars.show_discuss();var o,s,a,i,r,m,c,p,d,_;if(o=t("#para_heading-"+e).next("div.paragraph_wrapper"),0!=o.length){if(s=t("#para_wrapper-"+e+" .commentlist"),a=o.find("#respond"),i=addComment.getLevel(),CommentPress.theme.sidebars.activate_sidebar("comments"),r=!1,m=o.css("display"),"none"==m&&(r=!0),t.unhighlight_para(),""!==e&&(c=t("#textblock-"+e),("1"!=cp_promote_reading||r)&&(t.highlight_para(c),CommentPress.common.content.scroll_page(c))),"0"==cp_promote_reading){if("y"==cp_comments_open&&(p=t("#comment_post_ID").prop("value"),d=t("#para_wrapper-"+e+" .reply_to_para").prop("id"),_=d.split("-")[1]),a[0]||"y"==cp_comments_open&&addComment.moveFormToPara(_,e,p),a[0]&&!i)return void("y"==cp_comments_open?(addComment.moveFormToPara(_,e,p),"para_heading"==n?CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#respond"),cp_scroll_speed)):CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed));if(!a[0]&&s[0]&&!r)return void("y"==cp_comments_open?"para_heading"==n?CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#respond"),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed));if(!r&&s[0])return void("y"==cp_comments_open?"para_heading"==n?CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#respond"),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed));if(a[0]&&!s[0]&&!r)return void("y"==cp_comments_open?"para_heading"==n?CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#respond"),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed));r||s[0]||(o.css("display","none"),r=!0)}o.slideToggle("slow",function(){"1"==cp_promote_reading&&r?CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed):r&&("y"==cp_comments_open?"para_heading"==n?CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#respond"),cp_scroll_speed):CommentPress.common.comments.scroll_comments(t("#para_heading-"+e),cp_scroll_speed))})}}}},CommentPress.theme.modern={},CommentPress.theme.settings.init(),CommentPress.theme.DOM.init(),CommentPress.theme.header.init(),CommentPress.theme.navigation.init(),CommentPress.theme.content.init(),CommentPress.theme.sidebars.init(),CommentPress.theme.viewport.init(),jQuery(document).ready(function(e){CommentPress.theme.settings.dom_ready(),CommentPress.theme.DOM.dom_ready(),CommentPress.theme.header.dom_ready(),CommentPress.theme.navigation.dom_ready(),CommentPress.theme.content.dom_ready(),CommentPress.theme.sidebars.dom_ready(),CommentPress.theme.viewport.dom_ready(),CommentPress.common.comments.comment_rollovers(),e(document).on("commentpress-post-changed",function(t){var n,o,s;o=document.location.href,n=e(".editor_toggle a"),0!=n.length&&(s=n.attr("href"),nonce=s.split("?")[1],o+="?"+nonce,n.attr("href",o))}),"1"==cp_special_page?CommentPress.common.content.on_load_scroll_to_comment():CommentPress.theme.viewport.on_load_scroll_to_anchor(),e(document).trigger("commentpress-document-ready")});