$(document).ready(function() {
            
            jQuery('#email_content-tmce').click();
            
            //$('#dd_group_list').dataTable({"bPaginate": false,"bSort":false});
            
            jQuery('#dd_group_list').dataTable({
              "bPaginate":true,
              "sPaginationType":"full_numbers",
              "iDisplayLength": 10,
              "sPageButton": "paginate_button"
        });
        
            
            jQuery('.delete-link').live('click',function(event){
                if(confirm('Are you sure to delete?'))
                {
                    jQuery.ajax({
                        type:'POST',
                        url:jQuery(this).attr('href'),
                        data:"id="+jQuery(this).attr('id')+"&action=DELETE",
                        success: function(msg)
                        { 
                            if(msg==1){	alert('Successfully deleted'); window.location.reload(true); }
                            else if(msg==0){ alert("This member booked the class. So not able to delete member."); return false; }
                        }
                    });
                }
                event.preventDefault();
            });
            
            jQuery('.checkall').live('click',function(event){
                    if(jQuery(this).is(':checked')) jQuery('.checkmember').attr('checked',true); else jQuery('.checkmember').attr('checked',false);
            });
            
            jQuery('#email_template').live('change',function(event){
                LoadEmailTemplate(jQuery(this).val());
            });
            
            var emailids = [];
            var emails = "";
            
            jQuery('#send_email_member_btn').live('click',function(event){
                if(jQuery('.checkmember:checked').length>0)
                {
                    jQuery('.send_member_email_popup').slideDown(function(){ jQuery('html,body').scrollTop(jQuery('.send_member_email_popup').offset().top-40); });
                    
                    jQuery('.checkmember:checked').each(function(index,element){ emailids.push(jQuery(this).val()); emails+=jQuery(this).val()+", "; });
                    jQuery('#to_address').val(emailids);
                    jQuery('.to_address').html(emails.substring(0,emails.length-2)); 
                }
                else
                {
                    alert("Choose member to send email notification");	return false;
                }
            });
            
            jQuery('#toaddr_clear').live('click',function(event){ 
            emailids=[];  emails = ""; 
            jQuery('#to_address').val(emailids); //alert(jQuery('#to_address').val(''));
            jQuery('.to_address').html(emails.substring(0,emails.length-2));	
            });
            
            
            jQuery('#send_text_member_btn').live('click',function(){
                if(jQuery('.checkmember:checked').length>0)
                {
                    jQuery('.send_member_text_popup').slideDown(function(){ jQuery('html,body').scrollTop(jQuery('.send_member_text_popup').offset().top-40); });
                    var mobilenos = [];
                    var mobiles = "";
                    jQuery('.checkmember:checked').each(function(index,element){ mobilenos.push(jQuery(this).attr('alt')); mobiles+=jQuery(this).attr('alt')+", "; });
                    jQuery('#to_mobiles').val(mobilenos);
                    jQuery('.to_mobiles').html(mobiles.substring(0,mobiles.length-2));
                }
                else
                {
                    alert("Choose member to send text notification");	return false;
                }
            });
            
            jQuery('.shortcut_box .shortcut').live('click',function(event){
                if(jQuery(this).hasClass('shortcut_padding')==false) jQuery(this).addClass('shortcut_padding'); else jQuery(this).removeClass('shortcut_padding');
                var id = jQuery(this).attr('title');
                jQuery('#'+id).toggle();
            });
            
        });
        
        function Search_member()
        {
            var data = jQuery('#membersearchform').serialize(); 
            jQuery.ajax({
                type:'POST',
                url:"ffa/search_member_list.php",
                data:data,
                success: function(msg){ jQuery('#searched_member_list').html(msg); 
                //$('#ffa_member_list').dataTable({"bPaginate": false,"bSort":false});
                
                jQuery('#ffa_member_list').dataTable({
              "bPaginate":true,
              "sPaginationType":"full_numbers",
              "iDisplayLength": 10,
              "sPageButton": "paginate_button"
        });
                
                
                
                 }
            });
        }
        Search_member();
        
        function Export_member()
        {
            var data = jQuery('#membersearchform').serialize();
            var export_filed = []; 
            jQuery('.export_field:checked').each(function(index,element){ export_filed.push(jQuery(this).val()); });
            if(export_filed.length>0) window.open('ffa/memberexport.php?'+data+'&export_filed='+export_filed,'');
            else{ alert("Choose fields to export"); return false; }
        }