<?php
/*
 * Plugin Name: Afisha
 */


// Хук событие 'admin_menu', запуск функции 'mfp_Add_My_Admin_Link()'
add_action( 'admin_menu', 'mfp_Add_My_Admin_Link' );

// Добавляем новую ссылку в меню Админ Консоли
function mfp_Add_My_Admin_Link()
{
 add_menu_page(
 'My First Page', // Название страниц (Title)
 'Afisha', // Текст ссылки в меню
 'manage_options', // Требование к возможности видеть ссылку
 'afisha/afisha-page.php' // 'slug' - файл отобразится по нажатию на ссылку
 );
}

add_action( 'init', 'true_register_post_type_init' );

function true_register_post_type_init() {
	$labels = array(
		'name' => 'news',
		'singular_name' => 'new', // админ панель Добавить->Тур
		'add_new' => 'Новость',
		'add_new_item' => 'Добавить новость', // заголовок тега <title>
		'edit_item' => 'Редактировать новость',
		'new_item' => 'Новый запись',
		'all_items' => 'Все новости',
		'view_item' => 'Просмотр новостей на сайте',
		'search_items' => 'Искать новости',
		'not_found' =>  'Записей не найдено.',
		'not_found_in_trash' => 'В корзине нет записей.',
		'menu_name' => 'Новость' // ссылка в меню в админке
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_ui' => true, // показывать интерфейс в админке
		'has_archive' => true,
		'menu_icon' => get_stylesheet_directory_uri() .'/img/function_icon.png', // иконка в меню
		'menu_position' => 40, // порядок в меню
		'supports' => array( 'title', 'editor', 'thumbnail')
	);
	register_post_type('tures', $args);
}


//*****************************Metabox******************************//
class trueMetaBox {
	function __construct($options) {
		$this->options = $options;
		$this->prefix = $this->options['id'] .'_';
		add_action( 'add_meta_boxes', array( &$this, 'create' ) );
		add_action( 'save_post', array( &$this, 'save' ), 1, 2 );
	}
	function create() {
		foreach ($this->options['post'] as $post_type) {
			if (current_user_can( $this->options['cap'])) {
				add_meta_box($this->options['id'], $this->options['name'], array(&$this, 'fill'), $post_type, $this->options['pos'], $this->options['pri']);
			}
		}
	}
	function fill(){
		global $post; $p_i_d = $post->ID;
		wp_nonce_field( $this->options['id'], $this->options['id'].'_wpnonce', false, true );
		?>
		<table style="width:45%;" class="form-table"><tbody><?php
		foreach ( $this->options['args'] as $param ) {
			if (current_user_can( $param['cap'])) {
			?><tr><?php
if(!$value = get_post_meta($post->ID, $this->prefix .$param['id'] , true)) $value = $param['std'];
		switch ( $param['id'] ) {

              case 'field_1':
			  case 'field_2':
			  case 'field_5':
			  case 'field_6':
			  case 'field_7':
			 case 'field_8':
			 case 'field_9':
			                  { ?>
						<th scope="row"><label for="<?php echo $this->prefix .$param['id'] ?>"><?php echo $param['title'] ?></label></th>
						<td>
							<input name="<?php echo $this->prefix .$param['id'] ?>" type="<?php echo $param['type'] ?>" id="<?php echo $this->prefix .$param['id'] ?>" value="<?php echo $value ?>" placeholder="<?php echo $param['placeholder'] ?>" class="regular-text" /><br />
							<span class="description"><?php echo $param['desc'] ?></span>
						</td>
						<?php
						break;
					}

			  case 'select_0':
			  case 'select_1':
			  case 'select_2':
			  case 'select_3': { ?>
						<th scope="row"><label for="<?php echo $this->prefix .$param['id'] ?>"><?php echo $param['title'] ?></label></th>
						<td>
							<label for="<?php echo $this->prefix .$param['id'] ?>">
							<select name="<?php echo $this->prefix .$param['id'] ?>" id="<?php echo $this->prefix .$param['id'] ?>"><option>...</option><?php
								foreach($param['args'] as $val=>$name){
									?><option value="<?php echo $val ?>"<?php echo ( $value == $val ) ? ' selected="selected"' : '' ?>><?php echo $name ?></option><?php
								}
							?></select></label><br />
							<span class="description"><?php echo $param['desc'] ?></span>
						</td>
						<?php
						break;
					}

				}
			?></tr><?php
			}
		}
		?></tbody></table><?php
	}

function save($post_id, $post){
		if ( !wp_verify_nonce( $_POST[ $this->options['id'].'_wpnonce' ], $this->options['id'] ) ) return;
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
		if ( !in_array($post->post_type, $this->options['post'])) return;
		foreach ( $this->options['args'] as $param ) {
			if ( current_user_can( $param['cap'] ) ) {
				if ( isset( $_POST[ $this->prefix . $param['id'] ] ) && trim( $_POST[ $this->prefix . $param['id'] ] ) ) {
					update_post_meta( $post_id, $this->prefix . $param['id'], trim($_POST[ $this->prefix . $param['id'] ]) );
				} else {
					delete_post_meta( $post_id, $this->prefix . $param['id'] );
				}
			}
		}
	}
}



$list = ['ДОНБАСС' => 'ДОНБАСС', 'ЦСКА' => 'ЦСКА', 'ЗТР-Буревесник' => 'ЗТР-Буревесник', 'ZTR' => 'ZTR',
				'СКА-Львов' => 'СКА-Львов', 'Портовик' => 'Портовик', 'Одесса' => 'Одесса', 'Мотор' => 'Мотор',
				  'BSV Берн' => 'BSV Берн'];



//

if ( isset($_GET['post']) ){
$new_comand = get_post_meta($_GET['post'], 'meta3_field_8', true);
$list[$new_comand]=$new_comand;
}
$options = array(
	array( // первый метабокс
		'id'	=>	'meta1', // ID метабокса, а также префикс названия произвольного поля
		'name'	=>	'Предыдущий матч', // заголовок метабокса
		'post'	=>	array('page'), // типы постов для которых нужно отобразить метабокс
		'pos'	=>	'normal', // расположение, параметр $context функции add_meta_box()
		'pri'	=>	'high', // приоритет, параметр $priority функции add_meta_box()
		'cap'	=>	'edit_posts', // какие права должны быть у пользователя
		'args'	=>	array(
			    array(
				'id'			=>	'field_1', // атрибуты name и id без префикса, например с префиксом будет meta1_field_1
				'title'			=>	'Дата встречи', // лейбл поля
				'type'			=>	'text', // тип, в данном случае обычное текстовое поле
				'placeholder'	=>	'', // атрибут placeholder
				'desc'			=>	'Например: "12 Окт / Сб / 19:30"', // что-то типа пояснения, подписи к полю
				'cap'			=>	'edit_posts'
			),

				array(
				'id'			=>	'field_2', // атрибуты name и id без префикса, например с префиксом будет meta1_field_1
				'title'			=>	'Лига', // лейбл поля
				'type'			=>	'text', // тип, в данном случае обычное текстовое поле
				'placeholder'	=>	'', // атрибут placeholder
				'desc'			=>	'Например: "Суперлига"', // что-то типа пояснения, подписи к полю
				'cap'			=>	'edit_posts'
			),

			array(
				'id'			=>	'select_0',
				'title'			=>	'Хозяева',
				'type'			=>	'select', // выпадающий список
				'desc'			=>	'Выберите из выпадающего списка команду',
				'cap'			=>	'edit_posts',
				'args'			=>	$list // элементы списка задаются через массив args, по типу value=>лейбл
			),

			array(
				'id'			=>	'select_1',
				'title'			=>	'Гости',
				'type'			=>	'select', // выпадающий список
				'desc'			=>	'Выберите из выпадающего списка команду',
				'cap'			=>	'edit_posts',
				'args'			=>	$list // элементы списка задаются через массив args, по типу value=>лейбл
			),

			array(
				'id'			=>	'field_5', // атрибуты name и id без префикса, например с префиксом будет meta1_field_1
				'title'			=>	'Счет', // лейбл поля
				'type'			=>	'text', // тип, в данном случае обычное текстовое поле
				'placeholder'	=>	'', // атрибут placeholder
				'desc'			=>	'Например:"35:48"', // что-то типа пояснения, подписи к полю
				'cap'			=>	'edit_posts'
			)

		)
	),


 array( // второй метабокс
		'id'	=>	'meta2',
		'name'	=>	'Следующий матч',
		'post'	=>	array('post', 'page'), // не только для постов, но и для страниц
		'pos'	=>	'normal',
		'pri'	=>	'high',
		'cap'	=>	'edit_posts',
		'args'	=>	array(

			    array(
				'id'			=>	'field_6', // атрибуты name и id без префикса, например с префиксом будет meta1_field_1
				'title'			=>	'Дата', // лейбл поля
				'type'			=>	'text', // тип, в данном случае обычное текстовое поле
				'placeholder'	=>	'', // атрибут placeholder
				'desc'			=>	'Например: "12 Окт / Сб / 19:30"', // что-то типа пояснения, подписи к полю
				'cap'			=>	'edit_posts'
			),

				array(
				'id'			=>	'field_7', // атрибуты name и id без префикса, например с префиксом будет meta1_field_1
				'title'			=>	'Лига', // лейбл поля
				'type'			=>	'text', // тип, в данном случае обычное текстовое поле
				'placeholder'	=>	'', // атрибут placeholder
				'desc'			=>	'Например: "Суперлига"', // что-то типа пояснения, подписи к полю
				'cap'			=>	'edit_posts'
			),

			    array(
				'id'			=>	'select_2',
				'title'			=>	'Хозяева',
				'type'			=>	'select', // выпадающий список
				'desc'			=>	'Выберите из выпадающего списка команду',
				'cap'			=>	'edit_posts',
				'args'			=>	$list // элементы списка задаются через массив args, по типу value=>лейбл
			),


			    array(
				'id'			=>	'select_3',
				'title'			=>	'Гости',
				'type'			=>	'select', // выпадающий список
				'desc'			=>	'Выберите из выпадающего списка команду',
				'cap'			=>	'edit_posts',
				'args'			=>	$list // элементы списка задаются через массив args, по типу value=>лейбл
			)

	     )
	 ),



array( // third metabox
		'id'	=>	'meta3', // ID метабокса, а также префикс названия произвольного поля
		'name'	=>	'Добавить новую команду (которой нет с списках)', // заголовок метабокса
		'post'	=>	array('page'), // типы постов для которых нужно отобразить метабокс
		'pos'	=>	'normal', // расположение, параметр $context функции add_meta_box()
		'pri'	=>	'high', // приоритет, параметр $priority функции add_meta_box()
		'cap'	=>	'edit_posts', // какие права должны быть у пользователя
		'args'	=>	array(
			    array(
				'id'			=>	'field_8', // атрибуты name и id без префикса, например с префиксом будет meta1_field_1
				'title'			=>	'Название команды', // лейбл поля
				'type'			=>	'text', // тип, в данном случае обычное текстовое поле
				'placeholder'	=>	'', // атрибут placeholder
				'desc'			=>	'Например: "Портовик-85"', // что-то типа пояснения, подписи к полю
				'cap'			=>	'edit_posts'
			),



				array(
				'id'			=>	'field_9', // атрибуты name и id без префикса, например с префиксом будет meta1_field_1
				'title'			=>	'Ссылка на логотип', // лейбл поля
				'type'			=>	'text', // тип, в данном случае обычное текстовое поле
				'placeholder'	=>	'', // '' атрибут placeholder
				'desc'			=>	'Например: "http://handball.in.ua/main/data/club.jpg"', // что-то типа пояснения, подписи к полю
				'cap'			=>	'edit_posts'
			)

		)
	)


);

$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;

$post_id = isset($_GET['post']) ? $_GET['post'] : $_POST['post_ID'] ;
// checks for post/page ID
if ($post_id == '332'){
foreach ($options as $option) {
	$truemetabox = new trueMetaBox($option);
  }
}
