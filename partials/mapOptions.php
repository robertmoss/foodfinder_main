<               <div id="mapOptions" class="mapOptions">
                    <p class="center">Show Only:</p>
                     <form id="displayOptionsForm">
                        <?php
                             /* to do: add logic to remember users settings across page loads */
                             $categories = Utility::getRequestVariable('categories', '');
                             if (strlen($categories)>0) {
                                    $cat_array=explode(',',$categories);
                                    }
                                foreach(Utility::getList('categories',$tenantID,$userID) as $category) {
                                    $selected = ''; 
                                    if (strlen($categories)>0) {
                                        if (in_array($category['id'],$cat_array,false)) {
                                            $selected = ' checked';
                                        }
                                    }
                                    echo '<div class="checkbox">';
                                    echo '  <label><input type="checkbox" class="categoryInput" value="' . $category['id'] . '" name="' . $category['name'] . '"' . $selected . '> ';
                                    echo '<img src="' . $category['icon'] .'">' . $category['name'] . '</label>';
                                    echo '</div>';
                                }
                            ?>
                    </form>
                  </div>