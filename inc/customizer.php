<?php
/**
 * Theme Customizer — branding & company details.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', 'baydemir_customize_register' );

/**
 * Register customizer sections.
 */
function baydemir_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_panel(
		'baydemir_panel',
		array(
			'title'    => __( 'Baydemir Tema Ayarları', 'baydemir' ),
			'priority' => 30,
		)
	);

	/* —— Brand —— */
	$wp_customize->add_section(
		'baydemir_brand',
		array(
			'title' => __( 'Marka & Renkler', 'baydemir' ),
			'panel' => 'baydemir_panel',
		)
	);

	$wp_customize->add_setting(
		'baydemir_company_name',
		array(
			'default'           => 'Baydemir İnşaat',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'baydemir_company_name',
		array(
			'label'   => __( 'Şirket Adı', 'baydemir' ),
			'section' => 'baydemir_brand',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'baydemir_tagline',
		array(
			'default'           => 'Güven • Kalite • Zamanında Teslim',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'baydemir_tagline',
		array(
			'label'   => __( 'Slogan / Tagline', 'baydemir' ),
			'section' => 'baydemir_brand',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'baydemir_brand_mark',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'baydemir_brand_mark',
			array(
				'label'       => __( 'Marka Simgesi', 'baydemir' ),
				'description' => __( 'Footer ve CTA şeridinde görünür. PNG, JPG, WebP veya SVG yükleyebilirsiniz. Boş bırakırsanız varsayılan simge kullanılır.', 'baydemir' ),
				'section'     => 'baydemir_brand',
				'mime_type'   => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'baydemir_accent_color',
		array(
			'default'           => '#2f7dff',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'baydemir_accent_color',
			array(
				'label'   => __( 'Vurgu Rengi', 'baydemir' ),
				'section' => 'baydemir_brand',
			)
		)
	);

	$brand_colors = array(
		'baydemir_bg_color'    => array( __( 'Arka Plan Rengi', 'baydemir' ), '#07090f' ),
		'baydemir_text_color'  => array( __( 'Ana Metin Rengi', 'baydemir' ), '#ffffff' ),
		'baydemir_muted_color' => array( __( 'Soluk Metin Rengi', 'baydemir' ), '#a8b0c0' ),
	);

	foreach ( $brand_colors as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'   => $data[0],
					'section' => 'baydemir_brand',
				)
			)
		);
	}

	/* —— Contact —— */
	$wp_customize->add_section(
		'baydemir_contact',
		array(
			'title' => __( 'İletişim Bilgileri', 'baydemir' ),
			'panel' => 'baydemir_panel',
		)
	);

	$contact_fields = array(
		'baydemir_phone'    => array( 'Telefon', '0530 231 40 00', 'text' ),
		'baydemir_email'    => array( 'E-posta', 'ibrhmbaydemir@windowslive.com', 'text' ),
		'baydemir_address'  => array( 'Adres', 'Cumhuriyet Mahallesi 2. Gazi Sokak No:2 Kat:2 Merkez / Kütahya', 'textarea' ),
		'baydemir_hours'    => array( 'Çalışma Saatleri', 'Pazartesi – Cumartesi: 09:00 – 18:00', 'text' ),
		'baydemir_whatsapp' => array( 'WhatsApp Numarası', '905302314000', 'text' ),
	);

	foreach ( $contact_fields as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $data[0],
				'description' => 'baydemir_whatsapp' === $id
					? __( 'Ülke koduyla yazın (ör. 905302314000). Boş bırakırsanız buton görünmez.', 'baydemir' )
					: '',
				'section'     => 'baydemir_contact',
				'type'        => $data[2],
			)
		);
	}

	$wp_customize->add_setting(
		'baydemir_whatsapp_show',
		array(
			'default'           => '1',
			'sanitize_callback' => static function ( $value ): string {
				return $value ? '1' : '';
			},
		)
	);
	$wp_customize->add_control(
		'baydemir_whatsapp_show',
		array(
			'label'       => __( 'Yüzen WhatsApp butonunu göster', 'baydemir' ),
			'description' => __( 'Kapatırsanız sitede yüzen WhatsApp ikonu gizlenir.', 'baydemir' ),
			'section'     => 'baydemir_contact',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'baydemir_map_embed',
		array(
			'default'           => 'https://maps.google.com/maps?q=K%C3%BCtahya%20Merkez&t=&z=14&ie=UTF8&iwloc=&output=embed',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'baydemir_map_embed',
		array(
			'label'       => __( 'Harita Embed URL', 'baydemir' ),
			'description' => __( 'Google Maps → Paylaş → Haritayı yerleştir → src URL', 'baydemir' ),
			'section'     => 'baydemir_contact',
			'type'        => 'url',
		)
	);

	/* —— Homepage —— */
	$wp_customize->add_section(
		'baydemir_home',
		array(
			'title' => __( 'Ana Sayfa İçerikleri', 'baydemir' ),
			'panel' => 'baydemir_panel',
		)
	);

	$home_fields = array(
		'baydemir_hero_eyebrow' => array( 'Hero Üst Metin', 'Güven • Kalite • Zamanında Teslim', 'text' ),
		'baydemir_hero_title'   => array( 'Hero Başlık', 'Güvenle İnşa, [[Geleceğe]] Bırak.', 'text' ),
		'baydemir_hero_text'    => array(
			'Hero Açıklama',
			'Modern mimari, sağlam mühendislik ve zamanında teslim ilkeleriyle yaşanabilir yapılar inşa ediyoruz.',
			'textarea',
		),
		'baydemir_hero_video'   => array( 'Hero Video URL', '', 'url' ),
		'baydemir_about_title'  => array( 'Hakkımızda Başlık', 'Biz Kimiz?', 'text' ),
		'baydemir_about_text'   => array(
			'Hakkımızda Kısa Metin',
			'Baydemir İnşaat, 2008’den bu yana madencilik nakliyesinde kazandığı tecrübeyi 2020’de inşaat sektörüne taşıyarak prestijli yaşam alanları üretmektedir.',
			'textarea',
		),
	);

	foreach ( $home_fields as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $data[0],
				'description' => str_contains( $id, 'title' ) ? __( 'Vurgulu kelimeler için [[kelime]] kullanın.', 'baydemir' ) : '',
				'section'     => 'baydemir_home',
				'type'        => $data[2],
			)
		);
	}

	$wp_customize->add_setting(
		'baydemir_home_about_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'baydemir_home_about_image',
			array(
				'label'       => __( 'Ana Sayfa — Biz Kimiz Görseli', 'baydemir' ),
				'description' => __( 'Sadece ana sayfadaki Biz Kimiz bölümü. Hakkımızda sayfası görselinden bağımsızdır.', 'baydemir' ),
				'section'     => 'baydemir_home',
				'mime_type'   => 'image',
			)
		)
	);

	$hero_colors = array(
		'baydemir_hero_eyebrow_color'   => array( __( 'Hero Üst Metin Rengi', 'baydemir' ), '#2f7dff' ),
		'baydemir_hero_title_color'     => array( __( 'Hero Başlık Rengi', 'baydemir' ), '#ffffff' ),
		'baydemir_hero_text_color'      => array( __( 'Hero Açıklama Rengi', 'baydemir' ), '#a8b0c0' ),
		'baydemir_hero_highlight_color' => array(
			__( 'Hero Vurgu Rengi', 'baydemir' ),
			'#2f7dff',
			__( 'Başlıktaki [[vurgulu]] kelimeler için.', 'baydemir' ),
		),
	);

	foreach ( $hero_colors as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'       => $data[0],
					'description' => $data[2] ?? '',
					'section'     => 'baydemir_home',
				)
			)
		);
	}

	$wp_customize->add_setting(
		'baydemir_hero_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'baydemir_hero_image',
			array(
				'label'     => __( 'Hero Arka Plan Görseli', 'baydemir' ),
				'section'   => 'baydemir_home',
				'mime_type' => 'image',
			)
		)
	);

	/* —— Stats —— */
	$wp_customize->add_section(
		'baydemir_stats',
		array(
			'title' => __( 'İstatistikler', 'baydemir' ),
			'panel' => 'baydemir_panel',
		)
	);

	$stats = array(
		'baydemir_stat_1_number' => array( 'İstatistik 1 Sayı', '20+' ),
		'baydemir_stat_1_label'  => array( 'İstatistik 1 Etiket', 'Tamamlanan Proje' ),
		'baydemir_stat_2_number' => array( 'İstatistik 2 Sayı', '15+' ),
		'baydemir_stat_2_label'  => array( 'İstatistik 2 Etiket', 'Yıllık Deneyim' ),
		'baydemir_stat_3_number' => array( 'İstatistik 3 Sayı', '%100' ),
		'baydemir_stat_3_label'  => array( 'İstatistik 3 Etiket', 'Müşteri Memnuniyeti' ),
	);

	foreach ( $stats as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $data[0],
				'section' => 'baydemir_stats',
				'type'    => 'text',
			)
		);
	}

	/* —— CTA —— */
	$wp_customize->add_section(
		'baydemir_cta',
		array(
			'title' => __( 'Teklif / CTA', 'baydemir' ),
			'panel' => 'baydemir_panel',
		)
	);

	$wp_customize->add_setting(
		'baydemir_cta_text',
		array(
			'default'           => 'Projelerinizi güvenle hayata geçirelim.',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'baydemir_cta_text',
		array(
			'label'   => __( 'CTA Metni', 'baydemir' ),
			'section' => 'baydemir_cta',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'baydemir_cta_button',
		array(
			'default'           => 'Teklif Al',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'baydemir_cta_button',
		array(
			'label'   => __( 'CTA Buton Metni', 'baydemir' ),
			'section' => 'baydemir_cta',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'baydemir_cta_page',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'baydemir_cta_page',
		array(
			'label'   => __( 'CTA Hedef Sayfa', 'baydemir' ),
			'section' => 'baydemir_cta',
			'type'    => 'dropdown-pages',
		)
	);

	/* —— Social —— */
	$wp_customize->add_section(
		'baydemir_social',
		array(
			'title' => __( 'Sosyal Medya', 'baydemir' ),
			'panel' => 'baydemir_panel',
		)
	);

	foreach ( array( 'instagram', 'facebook', 'linkedin', 'youtube' ) as $network ) {
		$id = 'baydemir_social_' . $network;
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => ucfirst( $network ) . ' URL',
				'section' => 'baydemir_social',
				'type'    => 'url',
			)
		);
	}
}
