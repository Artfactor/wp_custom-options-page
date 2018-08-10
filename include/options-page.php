<?
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types'); //Разрешаем загружать дополнительные типы файлов

function my_plugin_options() {
  ?>
    <?if($_GET['action'] != 'edit' && $_GET['action']):?>
      <a href="<?=$_SERVER['REQUEST_URI']?>&action=edit">Редактировать</a>
    <?endif;?>
    <?if($_GET['action'] != 'new'):?>
      <a href="<?=$_SERVER['REQUEST_URI']?>&action=new">Добавить новый</a>
    <?endif;?>
    <?if($_GET['action'] != 'delete'):?>
      <a href="<?=$_SERVER['REQUEST_URI']?>&action=delete">Удалить существующие</a>
    <?endif;?>

  <?
  if($_GET['action'] == 'new'){
    my_plugin_add_new_option();
  }
  if($_GET['action'] == 'delete'){
    my_plugin_delete();
  }
  if($_GET['action'] == 'edit' || !$_GET['action']){
    my_plugin_edit_options();
  }
}

function my_plugin_add_new_option() {
  ?>
	  <form class="add_new_field" method="post" action="options.php" enctype="multipart/form-data">
      <?if(function_exists( 'wp_enqueue_media' )){
        wp_enqueue_media();
      }else{
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
      }?>
      <?php settings_fields( 'myoption-group_new' ); ?>
      <?php do_settings_sections( 'myoption-group_new' ); ?>
      <?echo '<pre>'. print_R(getOption('test'), 1) . '</pre>'?>
      <input type="hidden" class="new_field" name="new_option" value="" />
      <table class="form-table" width="500">
        <tr valign="top">
          <th scope="row">Название</th>
          <td><input type="text" name="name" value="" /></td>
        </tr>
        <tr valign="top">
          <th scope="row">Тип</th>
          <td>
              <select name="type">
                <option value="text">Текст</option>
                <option value="img">Фаил</option>
              </select>
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  <?
}

function my_plugin_delete() {
  ?>
  <form class="add_new_field" method="post" action="options.php" enctype="multipart/form-data">
    <?if(function_exists( 'wp_enqueue_media' )){
      wp_enqueue_media();
    }else{
      wp_enqueue_style('thickbox');
      wp_enqueue_script('media-upload');
      wp_enqueue_script('thickbox');
    }?>
    <?php settings_fields( 'myoption-group_new' ); ?>
    <?php do_settings_sections( 'myoption-group_new' ); ?>
    <?$options = getAllOptions();?>
    <table class="form-table" width="500">
      <?foreach($options as $key => $option):
        $name = json_decode($option->option_value)->name;
      ?>
        <tr valign="top">
          <th scope="row"><?=$name?></th>
          <td><button class="remove-option" value="<?=$option->option_name?>" >Удалить</button></td>
        </tr>
      <?endforeach;?>
    </table>
  </form>
<?
}


function my_plugin_edit_options() {
  ?>
	  <form method="post" action="options.php" enctype="multipart/form-data" class="edit_options">
      <?if(function_exists( 'wp_enqueue_media' )){
          wp_enqueue_media();
      }else{
          wp_enqueue_style('thickbox');
          wp_enqueue_script('media-upload');
          wp_enqueue_script('thickbox');
      }?>
      <?php settings_fields( 'myoption-group' ); ?>
      <?php do_settings_sections( 'myoption-group' ); ?>
      <?$options = getAllOptions();?>
      <table class="form-table" width="500">
          <?foreach($options as $key => $option):?>
            <?$optionObj = json_decode($option->option_value);?>
            <tr valign="top">
              <th scope="row"><?=$optionObj->name?></th>
              <?if($optionObj->type == 'text'):?>
                <td>
                  <input class="json_value" type="hidden" name="<?=$option->option_name?>" value="<?php echo esc_attr( get_option($option->option_name) ); ?>" />
                  <input class="text_value" size="60" type="text"  value="<?php echo esc_attr( $optionObj->value ); ?>" />
                </td>
              <?else:?>
                <td><?showOtionImg($option->option_name)?></td>
              <?endif;?>
            </tr>
          <?endforeach;?>
      </table>
      <?php submit_button(); ?>
    </form>
  <?
}

