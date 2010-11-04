<?php


function cpt_plugin_options()
    {
        $options = get_option('cpto_options');
        
        if (isset($_POST['form_submit']))
            {
                    
                $options['level'] = $_POST['level'];
                    
                echo '<div class="updated fade"><p>Settings Saved</p></div>';

                update_option('cpto_options', $options);   
            }
            
            $queue_data = get_option('ce_queue');
            
                    ?>
                      <div class="wrap"> 
                        <div id="icon-settings" class="icon32"></div>
                            <h2>General Setings</h2>
                           
                            <form id="form_data" name="form" method="post">   
                                <br />
                                <h2 class="subtitle">General</h2>                              
                                <table class="form-table">
                                    <tbody>
                            
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label>Minimum Level to use this plugin</label></th>
                                            <td>
                                                <select id="role" name="level">
                                                    <option value="0" <?php if ($options['level'] == "0") echo 'selected="selected"'?>>Subscriber</option>
                                                    <option value="1" <?php if ($options['level'] == "1") echo 'selected="selected"'?>>Contributor</option>
                                                    <option value="2" <?php if ($options['level'] == "2") echo 'selected="selected"'?>>Author</option>
                                                    <option value="5" <?php if ($options['level'] == "5") echo 'selected="selected"'?>>Editor</option>
                                                    <option value="8" <?php if ($options['level'] == "8") echo 'selected="selected"'?>>Administrator</option>
                                                </select>
                                            </td>
                                        </tr>
                                                                                
                                    </tbody>
                                </table>
                               
                    
                                <p class="submit">
                                    <input type="submit" name="Submit" class="button-primary" value="Save Settings">
                               </p>
                            
                                <input type="hidden" name="form_submit" value="true" />
                                
                            </form>
                            
                    <?php  
            echo '</div>';   
        
        
    }

?>