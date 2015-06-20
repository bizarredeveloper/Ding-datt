$(document).ready(function() {
                   
            jQuery('#dd_group_list1').dataTable({
              "bPaginate":true,
              "sPaginationType":"full_numbers",
              "iDisplayLength": 10,
              "sPageButton": "paginate_button"
        });
                    
            jQuery('.checkall').live('click',function(event){
                    if(jQuery(this).is(':checked')) jQuery('.checkmember').attr('checked',true); else jQuery('.checkmember').attr('checked',false);
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