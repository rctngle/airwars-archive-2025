<?php

function get_language() {
	global $post;

	$lang = 'en';
	if (($post && (in_array($post->post_name, ['ar']) || in_array($post->post_type, ['conflict_ar', 'about_ar']))) || get_query_var('lang') == 'ar') {
		$lang = 'ar';
	}

	if (($post && (in_array($post->post_name, ['he']) || in_array($post->post_type, ['conflict_he', 'about_he']))) || get_query_var('lang') == 'he') {
		$lang = 'he';
	}

	return $lang;
}

function sprintf_array($format, $arr) { 
	return call_user_func_array('sprintf', array_merge((array)$format, $arr)); 
} 

function dict_keyify($str) {
	return strtolower(trim(str_replace('-', '_', sanitize_title($str))));
}

function dictf($str, $lang = false, $params = []) {
	$lang = ($lang) ? $lang : get_language();


	$dictionary = [
		// new translation: declared instead of confirmed
		'locally_reported_civilian_deaths_us_led_coalition_iraq_and_syria' => [
			'en' => 'Locally reported civilian deaths from declared or likely US-led Coalition actions in Iraq and Syria for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by the US-led Coalition. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'الضحايا المدنيين المبلغ عنهم محليا من قصف التحالف التي تقوده الولايات المتحدة في العراق وسوريا والتي تم تقييمها من قبل الحروب الجوية بأنها عادلة ، أو تم تأكيدها من قبل التحالف الدولي بقيادة الولايات المتحدة. هذه الادعاءات تم جمعها بسبب <span class="statistic">%s</span> حادثة مزعومة',
		],
		'locally_reported_civilian_deaths_russian_military_syria' => [
			'en' => 'Locally reported civilian deaths from declared or likely Russian Military actions in Iraq and Syria for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by the Russian Military. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'الضحايا المدنيين المبلغ عنهم محليا بسبب الجيش الروسي في سوريا التي قام فريق الحروب الجوية بتقييمها على أنها عادلة  أو تم تأكيدها من قبل الجيش الروسي. تنشأ هذه من <span class="statistic">%s</span> حوادث مزعومة.',
		],
		'locally_reported_civilian_deaths_russian_military_ukraine' => [
			'en' => 'Locally reported civilian deaths from declared or likely Russian Military actions in Ukraine for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by the Russian Military. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => '',
		],
		'locally_reported_civilian_deaths_turkish_military_iraq_and_syria' => [
			'en' => 'Locally reported civilian deaths from declared or likely Turkish Military actions in Iraq and Syria for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by the Turkish Military. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'الضحايا المدنيين المبلغ عنهم محليا بسبب الجيش التركي في سوريا التي قام فريق الحروب الجوية بتقييمها على أنها عادلة  أو تم تأكيدها من قبل الجيش التركي. تنشأ هذه من <span class="statistic">%s</span> حوادث مزعومة.',
		],
		'locally_reported_civilian_deaths_all_belligerents_libya' => [
			'en' => 'Locally reported civilian deaths from declared or likely actions in Libya for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by a belligerent. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'الضحايا المدنيين المبلغ عنهم محليا  في ليبيا التي تم قام فريق الحروب الجوية بتقييمها على أنها عادلة أو تم تأكيدها من قبل أحد أطراف النزاع. تنشأ هذه من <span class="statistic">%s</span> حوادث مزعومة.',
		],
		'locally_reported_civilian_deaths_all_belligerents_libya_2011' => [
			'en' => 'Locally reported civilian deaths from declared or likely actions in Libya in 2011 reviewed to date for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by a belligerent. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'الضحايا المدنيون المبلغ عنهم محليا في ليبيا 2011 نتيجة ضربات محتملة أو معلنة تم قام فريق الحروب الجوية بتقييمها على أنها محتملة أو تم تأكيدها من قبل أحد أطراف النزاع. نشأت هذه من <span class="statistic">%s</span> حادثا منفصلا مزعوما.',
		],
		'locally_reported_civilian_deaths_israeli_military_iraq_and_syria' => [
			'en' => 'Locally reported civilian deaths from declared or likely Israeli Military actions in Iraq and Syria for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by the Israeli Military. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => '',
		],
		'locally_reported_civilian_deaths_israeli_military_the_gaza_strip' => [
			'en' => 'Locally reported civilian deaths from declared or likely Israeli Military strikes in the Gaza strip for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by the Israeli Military. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'القتلى المدنيين المبلغ عنهم محليًا جراء الغارات الإسرائيلية المعلنة أو المحتملة في الأراضي الفلسطينية والتي قيّمت الحروب الجوية (Airwars) التقارير بشأنها على أنها <span class="grading">مقبولة</span>، أو <span class="grading">أكدها</span> الجيش الإسرائيلي.والتي نشأت عن <span class="statistic">%s</span> حادثة منفصلة من حوادث ادت لسقوط ضحايا من المدنيين',
			'he' => 'דיווחים מקומיים על מקרי מוות אזרחיים כתוצאה מתקיפות ישראליות ודאיות או בעלות סבירות גבוהה ברצועת עזה, שלגביהם הדיווח הוערך על ידי איירוורס <span class+"grading">כאמין</span>, או אושרו על ידי הצבא הישראלי. מקורם ב-<span class="statistic">123</span> אירועים נפרדים.'
		],
		'locally_reported_civilian_deaths_palestinian_militants_israel' => [
			'en' => 'Locally reported civilian deaths from declared or likely Palestinian Militant actions in Israel for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by Palestinian Militants. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'عدد قتلى المدنيين المبلغ عنها محليًا جراء الأعمال العسكرية للفصائل الفلسطينية المعلنة أو المحتملة في الأراضي الإسرائيلة والتي قيّمت الحروب الجوية (Airwars) التقارير بشأنها على أنه يوجد دليل كافي لتأكيدها، أو <span class="grading">أكدها</span> الجيش الإسرائيلي. والتي نشأت عن <span class="statistic">%s</span> حادثة منفصلة من حوادث اادت لسقوط ضحايا من المدنيين.',
			'he' => 'דיווחים מקומיים אודות מקרי מוות אזרחיים כתוצאה מפעולות מוצהרות או בסבירות גבוהה של קבוצות מיליטנטיות פלסטיניות בישראל, שאותן איירוורס העריכה כאמינים, או <span class="grading">שהוכרו ואושרו</span> על ידי הכוחות הפלסטינים. מקורם ב -<span class="statistic">%s</span> אירועים נפרדים.',
		],
		'locally_reported_civilian_deaths_israeli_military_syria' => [
			'en' => 'Locally reported civilian deaths from declared or likely Israeli Military strikes in Syria for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by the Israeli Military. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'القتلى المدنيين المبلغ عنهم محليًا جراءالغارات الإسرائيلية المعلنة أو المحتملة في سوريا والتي قيّمت الحروب الجوية (Airwars) التقارير بشأنها على أنها <span class="grading">مقبولة</span>، أو <span class="grading">أكدها</span> الجيش الإسرائيلي. .والتي نشأت عن <span class="statistic">%s</span> حادثة منفصلة من حوادث ادت لسقوط ضحايا من المدنيين.',
			'he' => 'דיווחים מקומיים אודות מקרי מוות של אזרחים כתוצאה מתקיפות ישראליות גלויות או בסבירות גבוהה בסוריה, שאיירוורס העריכה <span class="grading">כאמין</span>, או <span class="grading">אושרו</span> על ידי הצבא הישראלי. מקורם ב-<span class="statistic">%s</span> אירועים נפרדים.',
		],
		'locally_reported_civilian_deaths_us_forces_somalia' => [
			'en' => 'Locally reported civilian deaths from declared or likely US Forces actions in Somalia for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by US Forces. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => '',
		],
		'locally_reported_civilian_deaths_us_forces_yemen' => [
			'en' => 'Locally reported civilian deaths from declared or likely US Forces actions in Yemen for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by US Forces. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => 'الضحايا المدنيون المبلغ عنهم محليا من عمليات امريكية في اليمن والتي تم تقييمها من قبل Airwars بأنها <span class="grading">عادلة</span> ، أو تم <span class="grading">تأكيدها</span> من قبل الولايات المتحدة. هذه الادعاءات تم جمعها بسبب <span class="statistic">%s</span> حادثة مزعومة',
		],
		'locally_reported_civilian_deaths_us_forces_pakistan' => [
			'en' => 'Locally reported civilian deaths from declared or likely US Forces actions in Pakistan for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by US Forces. These originate from <span class="statistic">%s</span> separate alleged incidents.',
			'ar' => '',
		],	
	
		// belligerent reported	
		'belligerent_reported_civilian_deaths_us_led_coalition_iraq_and_syria' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from US-led Coalition actions in Iraq and Syria, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'أعداد الضحايا المدنيين المؤكدة  بسبب قصف التحالف الدولي بقيادة الولايات المتحدة في العراق وسوريا ، والتي نشأت من <span class="statistic">%s</span> من الحوادث التي تم رصدها.',
		],
		'belligerent_reported_civilian_deaths_russian_military_syria' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from Russian Military actions in Syria, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'أعداد الضحايا المدنيين المؤكدة بسبب قصف الجيش الروسي في سوريا ، والتي نشأت من <span class="statistic">%s</span> الحوادث التي تم رصدها.',
		],
		'belligerent_reported_civilian_deaths_russian_military_ukraine' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from Russian Military actions in Ukraine, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => '',
		],
		'belligerent_reported_civilian_deaths_turkish_military_iraq_and_syria' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from Turkish Military actions in Iraq and Syria, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'أعداد الضحايا المدنيين المؤكدة  بسبب قصف الجيش التركي في العراق وسوريا ، والتي نشأت من <span class="incidents">%s</span> الحوادث التي تم رصدها',
		],
		'belligerent_reported_civilian_deaths_kurdish_military_iraq_and_syria' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from Kurdish Forces actions in Iraq and Syria, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'أعداد الضحايا المدنيين المؤكدة  بسبب الأعمال العسكرية ل القوات الكردية في العراق وسوريا ، والتي نشأت من <span class="incidents">%s</span> الحوادث التي تم رصدها.',
		],
		'belligerent_reported_civilian_deaths_israeli_military_iraq_and_syria' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from Israeli Military actions in Iraq and Syria, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => '',
		],
		'belligerent_reported_civilian_deaths_israeli_military_the_gaza_strip' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from Israeli Military strikes in the Gaza Strip, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'القتلى <span class="grading">المؤكدين</span> في صفوف المدنيين جراء الغارات الإسرائيلية في قطاع غزة ، والتي نشأت عن <span class="incidents">%s</span> حادثة منفصلة من حوادث ادت لسقوط ضحايا من المدنيين.',
			'he' => 'מקרי מוות של אזרחים <span class="grading">שנבדקו ואושרו</span>, כתוצאה מתקיפותישראליות ברצועת עזה, מתוך <span class="incidents">%s</span> מקרים נפרדים של פגיעה באזרחים.',
		],
		'belligerent_reported_civilian_deaths_palestinian_militants_israel' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from Palestinian Militant actions in Israel, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'القتلى <span class="grading">المؤكدين</span> في صفوف المدنيين جراء عمليات الفصائل الفلسطينية في إسرائيل ، والتي نشأت عن <span class="incidents">%s</span> حادثة منفصلة من حوادث ادت لسقوط ضحايا من المدنيين.',
			'he' => 'דיווחים <span class="grading">שאושרו</span> והוכרו אודות מות אזרחים כתוצאה מפעולות של קבוצות מיליטנטיות פלסטיניות בישראל, שמקורם ב-<span class="incidents">%s</span> מקרים נפרדים של פגיעה באזרחים.',
		],
		'belligerent_reported_civilian_deaths_israeli_military_syria' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from Israeli Military strikes in Syria, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'القتلى <span class="grading">المؤكدين</span> في صفوف المدنيين جراء الغارات الإسرائيلية في سوريا ، والتي نشأت من <span class="incidents">%s</span> حادثة منفصلة ألحقت أضرارًا بالمدنيين.',
			'he' => 'מקרי מוות של אזרחים <span class="grading">שאושרו</span>, כתוצאה מתקיפות ישראליות בסוריה, מקורם ב-<span class="incidents">%s</span> מקרים נפרדים בהם נפגעו אזרחים.'
		],
		'belligerent_reported_civilian_deaths_all_belligerents_libya' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from all belligerents’ actions in Libya, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'أعداد الضحايا المدنيين المؤكدة بسبب جميع أطراف النزاع في ليبيا والتي نشأت من <span class="incidents">%s</span> الحوادث التي تم رصدها',
		],
		'belligerent_reported_civilian_deaths_all_belligerents_libya_2011' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from all belligerents’ actions in Libya in 2011  reviewed to date, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'أعداد الضحايا المدنيين <span class="grading">المؤكدة</span> نتيجة أعمال جميع أطراف النزاع في ليبيا عام 2011 التي تم تقييمها حتى اليوم والتي نشأت عن <span class="incidents">%s</span> حادثا منفصلا لأذى المدنيين.',
		],
		'belligerent_reported_civilian_deaths_us_forces_somalia' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from US Forces actions in Somalia, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => '',
		],
		'belligerent_reported_civilian_deaths_us_forces_yemen' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from US Forces actions in Yemen, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => 'أعداد الضحايا المدنيين <span class="grading">المؤكدة</span> بسبب عمليات قوات الولايات المتحدة في اليمن ، والتي نتجت عن <span class="incidents">%s</span> من الحوادث التي تم رصدها.',
		],
		'belligerent_reported_civilian_deaths_us_forces_pakistan' => [
			'en' => '<span class="grading">Confirmed</span> civilian deaths, from US Forces actions in Pakistan, originating from <span class="incidents">%s</span> separate incidents of civilian harm.',
			'ar' => '',
		],
		'civilians_killed_in_country_over_days' => [
			'en' => '<span>%s – %s</span> civilians killed in <br/>%s over <span>%s days</span>',
			'ar' => 'مابين <span>%s – %s</span> مدني قتلوا على %s خلال <span>%s أيام</span>',
			'he' => 'דווח על הרג של <span>%s – %s</span> אזרחים ברצועת עזה במהלך <span>%s ימי הלחימה</span>',
		],
		'civilians_alleged_killed_in_country_over_days' => [
			'en' => '<span>%s – %s</span> civilians alleged killed in <br/>%s over <span>%s days</span>',
			'ar' => 'مابين <span>%s – %s</span> مدني يُدعى أنهم قتلوا في %s خلال <span>%s أيام</span>',
			'he' => 'דווח על הרג של <span>%s – %s</span> אזרחים ברצועת עזה במהלך <span>%s ימי הלחימה</span>',
		],
		'civilians_alleged_killed_in_country_by_israeli_strikes_over_days' => [
			'en' => '<span>%s – %s</span> civilians alleged killed in <br/>%s by Israeli strikes over <span>%s days</span>',
			'ar' => 'مابين <span>%s – %s</span> مدني يُدعى أنهم قتلوا في %s جراء الغارات العسكرية الإسرائيلية خلال <span>%s أيام</span>',
			'he' => 'דווח על הרג של <span>%s – %s</span> אזרחים %s בשל תקיפות ישראליות במהלך <span>%s ימי הלחימה</span>',
		],
		'civilians_killed_and_injured_in_incidents_multiple' => [
			'en' => '<span>%s–%s</span> civilians killed and <span>%s–%s</span> inured in <span>%s</span> incidents',
			'ar' => '<span>%s–%s</span> من المدنيين قتلوا و <span>%s–%s</span> جرحوا في <span>%s</span> من الحوادث',
			'he' => 'דווח על הרג של <span>%s–%s</span> אזרחים בסוריה בשל תקיפות ישראליות במהלך <span>%s</span> ימי הלחימה ',
		],
		'civilians_killed_and_injured_in_incidents_singular' => [
			'en' => '<span>%s–%s</span> civilians killed and <span>%s–%s</span> inured in <span>%s</span> incident',
			'ar' => '<span>%s–%s</span> من المدنيين قتلوا و <span>%s–%s</span> جرحوا في <span>%s</span> واحدة',
			'he' => '<span>%s–%s</span> אזרחים נהרגו ו-<span>%s–%s</span> נפצעו בתקרית <span>%s</span>',
		],







	];

	$translated = dict_lookup($dictionary, $str, $lang);

	return sprintf_array($translated, $params);
}


function dict($str, $lang = false) {
	$lang = ($lang) ? $lang : get_language();














	$dictionary = [
		'airwars' => [
			'en' => 'Airwars',
			'ar' => 'الحروب الجوية',
		],

		// month
		'january' => [
			'en' => 'January',
			'ar' => 'كانون الثاني/ يناير',
			'ar_google' => 'كانون الثاني',
		],
		'february' => [
			'en' => 'February',
			'ar' => 'شباط/ فبراير',
			'ar_google' => 'فبراير',
		],
		'march' => [
			'en' => 'March',
			'ar' => 'اذار/ مارس',
			'ar_google' => 'مارس',
		],
		'april' => [
			'en' => 'April',
			'ar' => 'نيسان/ أبريل',
			'ar_google' => 'أبريل',
		],
		'may' => [
			'en' => 'May',
			'ar' => 'ايار/ مايو',
			'ar_google' => 'قد',
		],
		'june' => [
			'en' => 'June',
			'ar' => 'حزيران/ يونيو',
			'ar_google' => 'يونيو',
		],
		'july' => [
			'en' => 'July',
			'ar' => 'تموز/ يوليو',
			'ar_google' => 'يوليو',
		],
		'august' => [
			'en' => 'August',
			'ar' => 'آب/ أغسطس',
			'ar_google' => 'أغسطس',
		],
		'september' => [
			'en' => 'September',
			'ar' => 'أيلول/ سبتمبر',
			'ar_google' => 'سبتمبر',
		],
		'october' => [
			'en' => 'October',
			'ar' => 'تشرين الأول/ اكتوبر',
			'ar_google' => 'شهر اكتوبر',
		],
		'november' => [
			'en' => 'November',
			'ar' => 'تشرين الثاني/ نوفمبر',
			'ar_google' => 'شهر نوفمبر',
		],
		'december' => [
			'en' => 'December',
			'ar' => 'كانون الأول/ ديسمبر',
			'ar_google' => 'ديسمبر',
		],

		// navigation
		'home' => [
			'en' => 'Home',
			'ar' => 'الرئيسية',
		],
		'menu' => [
			'en' => 'Menu',
			'ar' => 'القائمة',
		],


		// post
		'published' => [
			'en' => 'Published',
			'ar' => 'ما تم نشره',
		],



		// footer
		'subscribe_to_our_mailing_list' => [
			'en' => 'Subscribe to our mailing list',
			'ar' => 'اشترك في قائمتنا البريدية',
		],
		'email_address' => [
			'en' => 'Email address',
			'ar' => 'عنوان البريد الإلكتروني',
		],
		'subscribe' => [
			'en' => 'Subscribe',
			'ar' => 'اشترك',
		],
		'donate_with_paypal' => [
			'en' => 'Donate with Paypal',
			'ar' => 'تبرع مع باي بال',
		],
		'airwars_registration_information' => [
			'en' => 'Airwars is registered in England and Wales as a not-for profit company limited by guarantee, company no. 10314448.',
			'ar' => 'الحروب الجوية مسجلة في إنجلترا وويلز كشركة غير ربحية محدودة بالضمانة، رقم الشركة 10314448',
		],
		'airwars_address' => [
			'en' => 'Our registered address is: c/o Thompson Jenner LLP, 1 Colleton Crescent, Exeter, Devon EX2 4DG.',
			'ar' => ':عنواننا المسجل هو: c/o Thompson Jenner LLP, 1 Colleton Crescent, Exeter, Devon EX2 4DG',
		],
		'site_by' => [
			'en' => 'Site by',
			'ar' => 'الموقع من قبل',
		],
		'contact' => [
			'en' => 'Contact',
			'ar' => 'اتصل بنا',
		],

		// home
		'our_monitoring_of_civilian_harm' => [
			'en' => 'Our monitoring of civilian harm',
			'ar' => 'متابعة الأضرار المدنية',
		],
		'conflicts_monitored' => [
			'en' => 'Conflicts Monitored',
			'ar' => 'النزاعات التي تم رصدها',
		],
		'alleged_civilian_deaths_assessed' => [
			'en' => 'Alleged Civilian Deaths Assessed',
			'ar' => 'الوفيات المدنية المزعومة التي تم تقييمها',
		],
		'military_reports_archived' => [
			'en' => 'Military Reports Archived',
			'ar' => 'التقارير العسكرية المؤرشفة',
		],
		'victim_names_recorded' => [
			'en' => 'Victim names recorded across all assessments',
			'ar' => 'أسماء الضحايا المسجلة',
		],
		'civilian_casualties_archive' => [
			'en' => 'Civilian casualties archive',
			'ar' => 'أرشيف الضحايا المدنيين',
		],
		'victim_in_focus' => [
			'en' => 'Victim in focus',
			'ar' => 'أحد الضحايا',
		],

		
		// conflict
		'length_of_campaign' => [
			'en' => 'Length  of Campaign', // new translation
			'ar' => 'عدد أيام الحملة',
			'he' => 'משך המערכה',
		],
		'length_of_battle_of_kharkiv' => [
			'en' => 'Length  of Battle of Kharkiv', // new translation
		],
		'us_led_coalition_strikes_in_iraq' => [
			'en' => 'US-led Coalition Strikes in Iraq',
			'ar' => 'ضربات قوات التحالف الدولي بقيادة الولايات المتحدة في العراق',
		],
		'us_led_coalition_strikes_in_syria' => [
			'en' => 'US-led Coalition Strikes in Syria',
			'ar' => 'ضربات قوات التحالف الدولي بقيادة الولايات المتحدة في سوريا',
		],
		'russian_military_strikes_in_syria' => [
			// 'en' => 'Russian Military Strikes in Syria',
			'en' => 'Declared Russian Armed Sorties in Syria as of September 2019', // new translation
			'ar' => 'ضربات القوات الروسية في سوريا',
		],
		'turkish_military_strikes_in_iraq' => [
			'en' => 'Turkish Military Strikes in Iraq',
			'ar' => 'ضربات الجيش التركي في العراق',
		],
		'all_belligerents_strikes_in_libya' => [
			'en' => 'Strikes by all belligerents in Libya',
			'ar' => 'الضربات حسب كل المتحاربين في ليبيا',
		],
		'israeli_military_strikes_in_iraq' => [
			'en' => 'Israeli Military Strikes in Iraq',
			'ar' => '',
		],
		'turkish_military_strikes_in_syria' => [
			'en' => 'Turkish Military Strikes in Syria',
			'ar' => 'ضربات الجيش التركي في سوريا',
		],
		'israeli_military_strikes_in_syria' => [
			'en' => 'Israeli Military Strikes in Syria',
			// 'ar' => '',
		],
		'israeli_military_strikes_in_the_gaza_strip' => [
			'en' => 'Israeli Military Strikes in the Gaza Strip',
			'ar' => 'الضربات العسكرية الإسرائيلية على قطاع غزة',
			'he' => 'תקיפות צבאיות ישראליות ברצועות עזה',
		],
		'palestinian_militants_strikes_in_israel' => [
			'en' => 'Palestinian Militant Strikes in Israel',
			'ar' => 'الضربات العسكرية الفلسطينية في إسرائيل',
			'he' => 'תקיפות של קבוצות מיליטנטיות פלסטיניות בישראל',
		],
		'us_forces_strikes_in_somalia' => [
			'en' => 'Declared US actions in Somalia since 2007',
			// 'ar' => '',
		],
		// 'us_forces_strikes_in_yemen' => [
		// 	'en' => 'Declared US actions in Yemen since January 20, 2017',
		// 	'ar' => 'عمليات امريكية معلنة في اليمن منذ 20 يناير 2017',
		// ],
		'us_forces_strikes_in_yemen' => [
			'en' => 'Declared US actions in Yemen since 2002',
			'ar' => 'الإجراءات الأمريكية المعلنة في اليمن منذ عام 2002',
		],
		'us_forces_strikes_in_pakistan' => [
			'en' => 'Declared US actions in Pakistan since January 20, 2017',
			// 'ar' => '',
		],
		'civilian_casualties_from_palestinian_militant_actions_may_2021' => [
			'en' => 'Civilian Casualties from Palestinian Militant Actions May 2021',
			'ar' => 'الضحايا المدنيين جراء عمليات الكتائب الفلسطينية في مايو / أيار ٢٠٢١',
			'he' => 'נפגעים אזרחיים מפעולות פלגים חמושים פלסטינים במאי 2021',
		],
		'israeli_military_in_the_gaza_strip_may_2021' => [
			'en' => 'Israeli Military in the Gaza Strip May 2021',
			'ar' => 'الجيش الإسرائيلي في قطاع غزة  في مايو / أيار ٢٠٢١',
			'he' => 'צה"ל ברצועת עזה מאי 2021',
		],

		'strikes_in_iraq' => [
			'en' => 'Strikes in Iraq',
			'ar' => 'الضربات الجوية في العراق',
		],
		'strikes_in_syria' => [
			'en' => 'Strikes in Syria',
			'ar' => 'الضربات الجوية في سوريا',
		],
		'alleged_civilian_casualty_incidents_monitored' => [
			'en' => 'Alleged civilian casualty incidents assessed',
			'ar' => 'حوادث الضحايا المدنيين المزعومة',
			'he' => 'אירועים עם נפגעים אזרחיים שהוערכו',
		],

		// key findings
		'airwars_estimate_of_civilian_deaths' => [
			'en' => 'Airwars estimate of civilian deaths',
			'ar' => 'تقديرات الحروب الجوية لأعداد الضحايا المدنيين',
			'he' => 'אומדן איירוורס של מספר מקרי מוות אזרחיים',
		],
		'airwars_estimate_of_civilian_deaths_the_gaza_strip' => [
			'en' => 'Airwars estimate of civilian deaths in the Gaza Strip from IDF strikes, May 2021',
			'ar' => 'تقديرات الحروب الجوية لأعداد الضحايا المدنيين في قطاع غزة جراء غارات جيش الدفاع الإسرائيلي ، مايو / أيار ٢٠٢١',
			'he' => 'אומדן איירוורס של מספר מקרי מוות אזרחיים מתקיפות צה"ל בעזה במלחמת מאי 2021',
		],
		'airwars_estimate_of_civilian_deaths_syria' => [
			'en' => 'Airwars estimate of civilian deaths from IDF actions in Syria, 2013–present',
			'ar' => 'تقديرات الحروب الجوية لأعداد الضحايا المدنيين في سوريا نتيجة العمليات العسكرية لجيش الدفاع الإسرائيلي ، من ٢٠١٣ حتى الوقت الحالي',
			'he' => 'אומדן איירוורס של מספר מקרי מוות אזרחיים מתקיפות צה"ל בסוריה, 2013 - כיום',
		],
		'us_led_coalition_estimate_of_civilian_deaths' => [
			'en' => 'US-led Coalition estimate of civilian deaths',
			'ar' => 'تقدير عدد الضحايا المدنيين من قبل التحالف الدولي بقيادة الولايات المتحدة ',
		],
		'russian_military_estimate_of_civilian_deaths' => [
			'en' => 'Russian Military estimate of civilian deaths',
			'ar' => 'تقدير عدد الضحايا المدنيين من قبل الجيش الروسي',
		],
		'turkish_military_estimate_of_civilian_deaths' => [
			'en' => 'Turkish Military estimate of civilian deaths',
			'ar' => 'تقدير عدد الضحايا المدنيين من قبل الجيش التركي',
		],
		'kurdish_forces_estimate_of_civilian_deaths' => [
			'en' => 'Kurdish Forces estimate of civilian deaths',
			'ar' => 'تقدير عدد الضحايا المدنيين من قبل القوات الكردية',
		],
		'israeli_military_estimate_of_civilian_deaths' => [
			'en' => 'Israeli Military estimate of civilian deaths',
			'ar' => 'تقدير الجيش الإسرائيلي للقتلى المدنيين',
			'he' => 'אומדן הצבא הישראלי של מספר מקרי מוות אזרחים',
		],
		'palestinian_militants_estimate_of_civilian_deaths' => [
			'en' => 'Palestinian Militants estimate of civilian deaths',
			'ar' => 'القوات الفلسطينية تقدر عدد القتلى المدنيين',
			'he' => 'הערכת פלגים חמושים פלסטינים לגבי מספר מקרי מוות של אזרחים',
		],
		'all_belligerents_estimate_of_civilian_deaths' => [
			'en' => 'All belligerents in Libya estimate of civilian deaths',
			'ar' => 'عدد الضحايا المدنيين المقدرة من قبل جميع أطراف النزاع',
		],
		'us_forces_estimate_of_civilian_deaths' => [
			'en' => 'US Forces estimate of civilian deaths',
			'ar' => 'عدد الضحايا المدنيين المقدرة من قبل جميع أطراف النزاع',
		],

		'children_likely_killed' => [
			'en' => 'children likely killed',
			'ar' => 'الأطفال الذين قتلو على الأرجح',
			'he' => 'מספר הילדים שדווח כי נהרגו',
		],
		'belligerents' => [
			'en' => 'belligerents',
			'ar' => 'أطراف',
			'he' => 'כוחות צבאיים',
		],
		'women_likely_killed' => [
			'en' => 'women likely killed',
			'ar' => 'النساء اللواتي قتلن على الأرجح',
			'he' => 'מספר הנשים שדווח כי נהרגו',
		],
		'militants_reportedly_killed' => [
			'en' => 'militants reportedly killed',
			'ar' => 'عدد المسلحين الذين وردت أنباء عن مقتلهم',
			'he' => 'על פי הדיווחים נהרגו חמושים',
		],
		'militants_reportedly_injured' => [
			'en' => 'militants reportedly injured',
			'ar' => 'عدد المسلحين الجرحى حسبما ورد',
			'he' => 'על פי הדיווחים נפצעו חמושים',
		],
		'likely_injured' => [
			'en' => 'likely injured',
			'ar' => 'على الأرجح قد أصيبو',
			'he' => 'כנראה נפצעו',
		],
		'named_victims' => [
			'en' => 'named victims',
			'ar' => 'الضحايا الذين تم التعرف عليهم',
			'he' => 'קורבנות שזוהו בשם',
		],
		'confirmed_injured' => [
			'en' => 'civilians confirmed injured',
			'ar' => 'الاصابات المؤكدة',
			'he' => 'אזרחים שפציעתם אושרה',
		],
		'alleged_deaths' => [
			'en' => 'Alleged deaths',
			'ar' => 'الوفيات المزعومة',
			'he' => 'טענות על מקרי מוות ',
		],
		'confirmed_or_fair_confirmed' => [
			'en' => 'Confirmed or',
			'ar' => 'مؤكدة او',
			'he' => 'אושר או',
		],
		'confirmed_or_fair_fair' => [
			'en' => 'fair',
			'ar' => 'مقبولة',
			'he' => 'אמין',
		],		
		'civilian_deaths_for_which_the_reporting_was_assessed_by_airwars_as_weak' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Weak.',
			'ar' => 'عدد الضحايا المدنيين من تقارير تم تقييمها من قبل فريق الحروب الجوية أنها ضعيفة',
			'he' => 'מקרי מוות אזרחיים שלגביהם הדיווח הוערך על ידי איירוורס כחלש.',
		],
		'civilian_deaths_for_which_the_reporting_is_assessed_by_airwars_as_contested' => [
			'en' => 'Civilian deaths for which the reporting is assessed by Airwars as Contested.',
			'ar' => 'عدد الضحايا المدنيين من تقارير تم تقييمها من فريق الحروب الجوية أنها حوادث متنازع عليها.',
			'he' => 'מקרי מוות אזרחיים שאיירוורס העריך את דיווחם כלא סבירים',
		],
		'civilian_deaths_were_discounted_by_airwars_after_assessment' => [
			'en' => 'Civilian deaths were Discounted by Airwars after assessment.',
			'ar' => 'عدد الضحايا المدنيين من تقارير تم إهمالها من فريق الحروب الجوية بعد تقييمها.',
			'he' => 'מקרי מוות אזרחיים שאיירוורס העריך את דיווחם כשגויים',
		],
		'separate_alleged_incidents' => [
			'en' => 'separate alleged incidents',
			'ar' => 'حوادث مزعومة أخرى',
			'he' => 'אירועים נפרדים',
		],



		// gradings
		'grading_confirmed' => [
			'en' => 'Confirmed',
			'ar' => 'مؤكدة',
			'he' => 'אושר',
		],
		'grading_fair' => [
			'en' => 'Fair',
			'ar' => 'مقبولة',
			'he' => 'אמין',
		],
		'grading_weak' => [
			'en' => 'Weak',
			'ar' => 'ضعيفة',
			'he' => 'חלש',
		],
		'grading_contested' => [
			'en' => 'Contested',
			'ar' => 'متنازع عليها',
			'he' => 'שנוי במחלוקת',
		],
		'grading_discounted' => [
			'en' => 'Discounted',
			'ar' => 'غير محتسبة',
			'he' => 'הוערך כשגוי',
		],

		// grading labels
		'grading_label_confirmed' => [
			'en' => 'Confirmed:',
			'ar' => 'مؤكدة',
			'he' => 'אושר'
		],
		'grading_label_fair' => [
			'en' => 'Fair:',
			'ar' => 'مقبولة',
			'he' => 'אמין'
		],
		'grading_label_weak' => [
			'en' => 'Weak:',
			'ar' => 'ضعيفة',
			'he' => 'חלש'
		],
		'grading_label_contested' => [
			'en' => 'Contested:',
			'ar' => 'متنازع عليها',
			'he' => 'לא סביר'
		],
		'grading_label_discounted' => [
			'en' => 'Discounted:',
			'ar' => 'تم دحضها',
			'he' => 'הוערך כשגוי'
		],
		

		// grading descriptions
		// new translation: confirmed or likely
		'locally_reported_civilian_deaths_us_led_coalition_iraq_and_syria' => [
			'en' => 'Locally reported civilian deaths from US-led Coalition actions in Iraq and Syria.',
			'ar' => 'عدد الضحايا المدنيين الذي تم الإبلاغ عنه محليًا بسبب قصف التحالف الدولي بقيادة الولايات المتحدة في العراق و سوريا.',
		],
		'locally_reported_civilian_deaths_russian_military_syria' => [
			'en' => 'Locally reported civilian deaths from Russian Military actions in Syria.',
			'ar' => 'عدد الضحايا المدنيين الذي تم الإبلاغ عنه محليًا بسبب قصف الجيش الروسي في سوريا.',
		],
		'locally_reported_civilian_deaths_russian_military_ukraine' => [
			'en' => 'Locally reported civilian deaths from Russian Military actions in Ukraine.',
			'ar' => '',
		],
		'locally_reported_civilian_deaths_turkish_military_iraq_and_syria' => [
			'en' => 'Locally reported civilian deaths from Turkish Military actions in Iraq and Syria.',
			'ar' => 'عدد الضحايا المدنيين الذي تم الإبلاغ عنه محليًا بسبب قصف الجيش التركي في العراق و سوريا.',
		],
		'locally_reported_civilian_deaths_israeli_military_iraq_and_syria' => [
			'en' => 'Locally reported civilian deaths from Israeli Military actions in Iraq and Syria.',
			'ar' => '',
		],
		'locally_reported_civilian_deaths_israeli_military_the_gaza_strip' => [
			'en' => 'Locally reported civilian deaths from Israeli Military strikes in the Gaza strip.',
			'ar' => 'القتلى المدنيين المبلغ عنهم محليا جراء االغارات الإسرائيلية على قطاع غزة.',
			'he' => 'דיווח מקומי על מקרי מוות של אזרחים כתוצאה מתקיפות ישראליות ברצועת עזה.',
		],
		'locally_reported_civilian_deaths_palestinian_militants_israel' => [
			'en' => 'Locally reported civilian deaths from Palestinian Militants actions in Israel.',
			'ar' => 'عدد القتلى المدنيين المبلغ عنها محليا جراء العمليات العسكرية للفصائل الفلسطينية في إسرائيل.',
			'he' => 'דיווחים מקומיים אודות מקרי מוות של אזרחים כתוצאה מפעולות קבוצות מיליטנטיות פלסטיניות בישראל.',
		],
		'locally_reported_civilian_deaths_israeli_military_syria' => [
			'en' => 'Locally reported civilian deaths from Israeli Military strikes in Syria.',
			'ar' => 'القتلى المدنيين المبلغ عنهم محليا جراءالغارات الإسرائيلية في سوريا.',
			'he' => 'דיווחים מקומיים על מקרי מוות של אזרחים כתוצאה מתקיפות ישראליות בסוריה.',
		],
		'locally_reported_civilian_deaths_all_belligerents_libya' => [
			'en' => 'Locally reported civilian deaths from actions in Libya.',
			'ar' => 'عدد الضحايا المدنيين التي تم الإبلاغ عنه محليًا من قبل جميع أطراف النزاع في ليبيا.',
		],
		'locally_reported_civilian_deaths_all_belligerents_libya_2011' => [
			'en' => 'Locally reported civilian deaths from actions in Libya in 2011 reviewed to date.',
			'ar' => 'عدد الضحايا المدنيين الذي تم الإبلاغ عنه محليًا في ليبيا 2011 والذي تم تقييمه حتى اليوم.',
		],
		'locally_reported_civilian_deaths_us_forces_somalia' => [
			'en' => 'Locally reported civilian deaths from US Forces actions in Somalia.',
			'ar' => '',
		],
		'locally_reported_civilian_deaths_us_forces_yemen' => [
			'en' => 'Locally reported civilian deaths from US Forces actions in Yemen.',
			'ar' => 'الضحايا المدنيون المبلغ عنهم محليا نتيجة عمليات امريكية في اليمن',
		],
		'locally_reported_civilian_deaths_us_forces_pakistan' => [
			'en' => 'Locally reported civilian deaths from US Forces actions in Pakistan.',
			'ar' => '',
		],

		'civilian_deaths_assessed_fair_us_led_coalition_iraq_and_syria' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by the US-led Coalition.',
			'ar' => 'عدد الضحايا المدنيين الذي تم تقيمه من قبل فريق الحروب الجوية بأنها عادلة ، أو تم تأكيدها من قبل التحالف الدولي بقيادة الولايات المتحدة.',
		],
		'civilian_deaths_assessed_fair_russian_military_syria' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by the Russian Military.',
			'ar' => 'عدد الضحايا المدنيين الذي تم تقيمه من قبل فريق الحروب الجوية بأنها عادلة ، أو تم تأكيدها من قبل الجيش الروسي.',
		],
		'civilian_deaths_assessed_fair_russian_military_ukraine' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by the Russian Military.',
			'ar' => '',
		],
		'civilian_deaths_assessed_fair_israeli_military_iraq_and_syria' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by the Israeli Military.',
			'ar' => '',
		],
		'civilian_deaths_assessed_fair_israeli_military_the_gaza_strip' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by the Israeli Military.',
			'ar' => 'القتلى المدنيين التي قيّمت الحروب الجوية (Airwars) التقارير حولهم على أنه يوجد دليل كافي لتأكيد حدوثها، أو أكدها الجيش الإسرائيلي.',
			'he' => 'מקרי מוות אזרחיים שהוערכו על ידי איירוורס כאמינים, או שאושרו על ידי הצבא הישראלי.',
		],
		'civilian_deaths_assessed_fair_israeli_military_syria' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by the Israeli Military.',
			'ar' => 'القتلى المدنيين التي قيّمت الحروب الجوية (Airwars) التقارير حولهم على أنه يوجد دليل كافي لتأكيد حدوثها، أو أكدها الجيش الإسرائيلي.',
			'he' => 'מקרי מוות אזרחיים שאיירוורס העריכה כאמינים, או שאושרו על ידי הצבא הישראלי.',
		],
		'civilian_deaths_assessed_fair_palestinian_militants_israel' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by Palestinian Militants.',
			'ar' => 'القتلى المدنيين التي قيّمت الحروب الجوية (Airwars) التقارير حولهم على أنه يوجد دليل كافي لتأكيد حدوثها، أو أكدتها الفصائل الفلسطينية.',
			'he' => 'מקרי מוות של אזרחים שאיירוורס העריכה כאמינים, או שאושרו על ידי פלגים חמושים פלסטינים.',
		],
		'civilian_deaths_assessed_fair_turkish_military_iraq_and_syria' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by the Turkish Military.',
			'ar' => 'عدد الضحايا المدنيين الذي تم تقيمه من قبل فريق الحروب الجوية بأنها عادلة ، أو تم تأكيدها من قبل الجيش التركي.',
		],
		'civilian_deaths_assessed_fair_all_belligerents_libya' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by one ore more belligerents in Libya.',
			'ar' => 'عدد الضحايا المدنيين الذي تم تقيمه من قبل فريق الحروب الجوية بأنها عادلة ، أو تم تأكيدها من قبل أحد أطراف النزاع',
		],
		'civilian_deaths_assessed_fair_all_belligerents_libya_2011' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by one ore more belligerents in Libya in 2011 reviewed to date.',
			'ar' => 'عدد الضحايا المدنيين الذي تمت مراجعته حتى اليوم والذي تم تقييمه من قبل فريق الحروب الجوية بأنه محتمل أو تم تأكيده من قبل واحد من أطراف النزاع أو أكثر في ليبيا 2011',
		],
		'civilian_deaths_assessed_fair_us_forces_somalia' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by US Forces.',
			'ar' => '',
		],
		'civilian_deaths_assessed_fair_us_forces_yemen' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by US Forces.',
			'ar' => 'عدد الضحايا المدنيين من إبلاغ تم تقييمه من قبل فريق الحروب الجوية بأنه مقبول، أو تم تأكيده من قبل قوات الولايات المتحدة.',
		],
		'civilian_deaths_assessed_fair_us_forces_pakistan' => [
			'en' => 'Civilian deaths for which the reporting was assessed by Airwars as Fair, or have been Confirmed by US Forces.',
			'ar' => '',
		],

		// tooltips
		'a_specific_belligerent_has_accepted_responsibility_for_civilian_harm' => [
			'en' => 'A specific belligerent has accepted responsibility for civilian harm.',
			'ar' => 'قام أحد أطراف النزاع بالاعتراف بتحمل مسؤلية الاضرار التي أصابت المدنيين',
			'he' => 'כוח צבאי קיבל על עצמו את האחריות לפגיעה באזרחים',
		],
		'reported_by_two_or_more_credible_sources_with_likely_or_confirmed_near_actions_by_a_belligerent' => [
			'en' => 'Reported by two or more credible sources, with likely or confirmed near actions by a belligerent.',
			'ar' => 'ذكرت من قبل مصدرين موثوقين أو أكثر ، مع احتمال أو تأكيد أعمال قريبة من قبل أحد أطراف النزاع.',
			'he' => 'דווח על ידי שני מקורות אמינים או יותר, עם פעולות קרובות (במקום או בזמן) שבסבירות גבוהה או אושרו על ידי כוח צבאי.',
		],
		'single_source_claim_though_sometimes_featuring_significant_information' => [
			'en' => 'Single source claim, though sometimes featuring significant information.',
			'ar' => 'ادعاء حصول ضربة جوية مزعومة تم التبليغ عنها من مصدر واحد. لكن أحيانا يحتوي التبليغ على معلومات مهمة',
			'he' => 'טענה ממקור יחידי, לעתים כולל מידע משמעותי.',
		],
		'competing_claims_of_responsibility_eg_multiple_belligerents_or_casualties_also_attributed_to_ground_forces' => [
			'en' => 'Competing claims of responsibility e.g. multiple belligerents, or casualties also attributed to ground forces.',
			'ar' => 'عدة ادعاءات لتحمل المسؤلية, على سبيل المثال, عدة أطراف نزاع أو قتلى بسبب قوات موجودة في ميدان الصراع',
			'he' => 'טענות מתחרות לאחריות למשל. מספר כוחות צבאיים מעורבים, או ייחוס הנפגעים לאירוע קרקעי',
		],
		'those_killed_were_combatants_or_other_parties_most_likely_responsible' => [
			'en' => 'Those killed were combatants, or other parties most likely responsible.',
			'ar' => 'القتلى كانو مقاتلين أو كان هناك أطراف أخرى مسؤلة',
			'he' => 'ההרוגים היו חמושים, או מעורבים אחרים, ככל הנראה, אחראים.',
		],

		// conflict graphs

		'chart_legend' => [
			'en' => 'Chart legend:',
			'ar' => 'شرح التشارت',
			'he' => 'מקרא:',
		],
		'view_this_chart_as' => [
			'en' => 'View this chart as:',
			'ar' => 'اظهار التشارت',
			'he' => 'הצגת תרשים בצורה',
		],
		'multiples' => [
			'en' => 'Multiples',
			'ar' => 'متعدد',
			'he' => 'פרוסה',
		],
		'stacked' => [
			'en' => 'Stacked',
			'ar' => 'مكوم',
			'he' => 'מקובצת',
		],
		'best_for_comparing_an_individual_group_over_time' => [
			'en' => 'Best for comparing an <strong>individual</strong> group over time',
			'ar' => 'مقارنة لاجمالي المجاميع خلال وقت',
			'he' => 'המתאים ביותר להשוואת קבוצה בודדת לאורך זמן',
		],
		'best_for_comparing_total_totals_over_time' => [
			'en' => 'Best for comparing <strong>totals</strong> over time',
			'ar' => 'مقارنة لمجموعات معينة خلال وقت',
			'he' => 'המתאים ביותר להשוואת סך כל הנפגעים',
		],
		'source' => [
			'en' => 'Source:',
			'ar' => 'المصدر:',
			'he' => 'מָקוֹר:',
		],
		'credit' => [
			'en' => 'Credit:',
			'ar' => 'المصدر:',
			'he' => 'זכויות יוצרים:',
		],
		'airwars_graphic' => [
			'en' => 'Airwars Graphic',
			'ar' => 'جراف الحروب الجوية',
			'he' => 'Airwars Graphic',
		],

		'reported_civilian_deaths_from_us_led_coalition_strikes_in_iraq' => [
			'en' => 'Reported civilian deaths from US-led Coalition strikes in Iraq',
			'ar' => 'عدد الضحايا المدنيين الذي تم الابلاغ عنه في العراق بسبب قصف قوات التحالف الدولي بقيادة الولايات المتحدة',
		],
		'reported_civilian_deaths_from_us_led_coalition_strikes_in_syria' => [
			'en' => 'Reported civilian deaths from US-led Coalition strikes in Syria',
			'ar' => 'عدد الضحايا المدنيين الذي تم الابلاغ عنه في سوريا بسبب قصف قوات التحالف الدولي بقيادة الولايات المتحدة',
		],
		'reported_civilian_deaths_from_russian_military_strikes_in_syria' => [
			'en' => 'Reported civilian deaths from Russian Military strikes in Syria',
			'ar' => 'عدد الضحايا المدنيين الذي تم الابلاغ عنه في سوريا بسبب القصف الروسي',
		],
		'reported_civilian_deaths_from_russian_military_strikes_in_ukraine' => [
			'en' => 'Reported civilian deaths from Russian Military strikes in Ukraine',
			'ar' => '',
		],
		'reported_civilian_deaths_from_turkish_military_strikes_in_iraq' => [
			'en' => 'Reported civilian deaths from Turkish Military strikes in Iraq',
			'ar' => 'عدد الضحايا المدنيين الذي تم الابلاغ عنه في العراق بسبب القصف التركي',
		],
		'reported_civilian_deaths_from_israeli_military_strikes_in_iraq' => [
			'en' => 'Reported civilian deaths from Israeli Military strikes in Iraq',
			'ar' => '',
		],
		'reported_civilian_deaths_from_turkish_military_strikes_in_syria' => [
			'en' => 'Reported civilian deaths from Turkish Military strikes in Syria',
			'ar' => 'عدد الضحايا المدنيين الذي تم الابلاغ عنه في سوريا بسبب القصف التركي',
		],
		'reported_civilian_deaths_from_kurdish_forces_strikes_in_syria' => [
			'en' => 'Reported civilian deaths from Kurdish Forces strikes in Syria',
			'ar' => 'عدد الضحايا المدنيين الذي تم الابلاغ عنه في سوريا بسبب القصف الكردي',
		],
		'reported_civilian_deaths_from_israeli_military_strikes_in_syria' => [
			'en' => 'Reported civilian deaths from Israeli Military strikes in Syria, 2013–2021',
			'ar' => 'القتلى المدنيين المبلغ عنهم بسبب قصف الجيش الإسرائيلي  في سوريا',
			'he' => 'אזרחים שדווח כי נהרגו כתוצאה מתקיפות ישראליות בסוריה, 2013-2021',
		],
		'reported_civilian_deaths_from_israeli_military_strikes_in_the_gaza_strip' => [
			'en' => 'Reported civilian deaths from Israeli Military strikes in the Gaza Strip, May 2021',
			'ar' => 'القتلى المدنيين المبلغ عنهم من الضربات العسكرية الإسرائيلية على قطاع غزة',
			'he' => 'אזרחים שדווח כי נהרגו כתוצאה מתקיפות צבאיות ישראליות ברצועת עזה, מאי 2021',
		],
		'reported_civilian_deaths_from_palestinian_militants_strikes_in_israel' => [
			'en' => 'Reported civilian deaths from Palestinian Militant strikes in Israel, May 2021',
			'ar' => 'القتلى المدنيين المبلغ عنهم من ضربات الفصائل الفلسطينية على إسرائيل',
			'he' => 'אזרחים שדווח כי נהרגו כתוצאה מירי רקטות של קבוצות מיליטניות פלסטיניות בישראל, מאי 2021',
		],
		'reported_civilian_deaths_from_strikes_in_libya' => [
			'en' => 'Reported civilian deaths from all belligerents’ strikes in Libya',
			'ar' => 'عدد الضحايا المدنيين الذي  تم الابلاغ عنه في ليبيا بسبب قصف جميع أطراف النزاع',
		],
		'reported_civilian_deaths_from_us_forces_strikes_in_somalia' => [
			'en' => 'Reported civilian deaths from US Forces strikes in Somalia',
			'ar' => '',
		],
		'reported_civilian_deaths_from_us_forces_strikes_in_yemen' => [
			'en' => 'Reported civilian deaths from US Forces strikes in Yemen',
			'ar' => 'عدد الضحايا المدنيين الذي تم الإبلاغ عنه بسبب ضربات امريكية',
		],
		'reported_civilian_deaths_from_us_forces_strikes_in_pakistan' => [
			'en' => 'Reported civilian deaths from US Forces strikes in Pakistan',
			'ar' => '',
		],

		'militant_deaths_per_year_in_somalia' => [
			'en' => 'Militant deaths per year in Somalia',
			'ar' => '',
		],
		'militant_deaths_per_year_in_yemen' => [
			'en' => 'Militant deaths per year in Yemen',
			'ar' => 'عدد القتلى المسلحين سنويا في اليمن',
		],
		'militant_deaths_per_year_in_pakistan' => [
			'en' => 'Militant deaths per year in Pakistan',
			'ar' => '',
		],
		'militant_deaths_per_year' => [
			'en' => 'Militant deaths per year',
			'ar' => 'عدد القتلى المسلحين سنويا',
		],


		'declared_and_alleged_us_actions_in_somalia' => [
			'en' => 'Declared and alleged US actions in Somalia',
			'ar' => '',
		],
		'declared_and_alleged_us_actions_in_yemen' => [
			'en' => 'Declared and alleged US actions in Yemen',
			'ar' => 'العمليات الأمريكية المعلنة والمزعومة في اليمن',
		],
		'declared_and_alleged_us_actions_in_pakistan' => [
			'en' => 'Declared and alleged US actions in Pakistan',
			'ar' => '',
		],
		'declared_and_alleged_us_actions' => [
			'en' => 'العمليات الأمريكية المعلنة والمزعومة',
			'ar' => '',
		],




		'conlict_graph_due_to_large_variations_in_the_quality_of_reporting' => [
			'en' => 'Due to large variations in the quality of reporting, Airwars provisionally grades allegations of civilian harm using a standardised methodology across all belligerents and conflicts. The five categories are explained in full on our Methodology page. Individual events are recorded in the Civilian Casualties pages.',
			'ar' => 'ونظراً للاختلافات الكبيرة في جودة التقارير المدنية ، يقوم فريق الحروب الجوية مؤقتاً بتصنيف مزاعم عن الأضرار المدنية باستخدام منهجية موحدة في جميع النزاعات. يتم شرح الفئات الخمس بالكامل في صفحة المنهجية الخاصة بنا. يتم تسجيل الأحداث الفردية في صفحات الضحايا المدنيين.',
			'he' => 'בשל הבדלים גדולים באיכות הדיווח, איירוורס מדרגת באופן זמני את האשמות על פגיעה באזרחים תוך שימוש במתודולוגיה קבועה לכל הכוחות המעורבים ובכלל הקונפליקטים. חמש הקטגוריות מוסברות במלואן בדף המתודולוגיה שלנו. אירועים בודדים מתועדים בדפי נפגעים אזרחיים.',
		],

		'airwars_russia_data_only_runs_to_october_2016_and_is_therefore_incomplete' => [
			'en' => 'Airwars Russia data only runs to October 2016 and is therefore incomplete.',
			'ar' => 'تمتد بيانات القصف الروسي لدى الحروب الجوية الى تشرين الأول/أكتوبر من عام 2016 و لذلك فهي غير كاملة',
		],

		// 

		// conflict map

		'israel_and_gaza_2023' => [
			'en' => 'Israel and Gaza 2023',
			'ar' => 'Israel and Gaza 2023',
		],
		'us_led_coalition_in_iraq_syria' => [
			'en' => 'US-led Coalition in Iraq & Syria',
			'ar' => 'قوات التحالف الدولي بقيادة الولايات المتحدة  في العراق و سوريا',
		],
		'us_led_coalition_in_iraq' => [
			'en' => 'US-led Coalition in Iraq',
			'ar' => 'التحالف الدولي بقيادة الولايات المتحدة في العراق',
		],
		'us_led_coalition_in_syria' => [
			'en' => 'US-led Coalition in Syria',
			'ar' => 'التحالف الدولي بقيادة الولايات المتحدة في سوريا',
		],
		'russian_military_in_syria' => [
			'en' => 'Russian Military in Syria',
			'ar' => 'الجيش الروسي في سوريا',
		],
		'russian_military_in_ukraine' => [
			'en' => 'Russian Military in Ukraine',
			'uk' => 'Російська військова в Україні',
			'ar' => 'العسكرية الروسية في أوكرانيا',
		],
		'russian_military_in_Kharkiv_ukraine' => [
			'en' => 'Russian Military in Kharkiv, Ukraine',
			'uk' => 'Російська військова в Харкові, Україна',
			'ar' => 'العسكرية الروسية في خاركيف، أوكرانيا',
		],
		'turkish_military_in_iraq_syria' => [
			'en' => 'Turkish Military in Iraq & Syria',
			'ar' => 'الجيش التركي في العراق و سوريا',
		],
		'turkish_military_in_iraq' => [
			'en' => 'Turkish Military in Iraq',
			'ar' => 'الجيش التركي في العراق',
		],
		'turkish_military_in_syria' => [
			'en' => 'Turkish Military in Syria',
			'ar' => 'الجيش التركي في سوريا',
		],
		'israeli_military_in_iraq_syria' => [
			'en' => 'Israeli Military in Iraq & Syria',
			'ar' => '',
		],
		'israeli_military_in_iraq' => [
			'en' => 'Israeli Military in Iraq',
			'ar' => '',
		],
		'israeli_military_in_syria' => [
			'en' => 'Israeli Military in Syria',
			'ar' => '',
		],
		'all_belligerents_in_libya' => [
			'en' => 'All Belligerents in Libya',
			'ar' => 'جميع أطراف النزاع في ليبيا',
		],
		'all_belligerents_in_libya_2012_present' => [
			'en' => 'All Belligerents in Libya, 2012–present',
			'ar' => 'جميع أطراف النزاع في ليبيا من عام ٢٠١٢ الى الوقت الحالي‎',
		],

		'all_belligerents_in_libya_2011' => [
			'en' => 'All Belligerents in Libya 2011',
			'ar' => '',
		],
		'us_forces_in_somalia' => [
			'en' => 'US Forces in Somalia',
			'ar' => '',
		],
		'us_forces_in_yemen' => [
			'en' => 'US Forces in Yemen',
			'ar' => 'القوات الأمريكية في اليمن',
		],
		'us_forces_in_pakistan' => [
			'en' => 'US Forces in Pakistan',
			'ar' => '',
		],
		'us_forces_in_yemen_trump' => [
			'en' => 'US Forces in Yemen: Trump',
			'ar' => 'القوات الأمريكية في اليمن: ترمب',
		],
		'us_forces_in_pakistan_trump' => [
			'en' => 'US Forces in Pakistan: Trump',
			'ar' => '',
		],
	
		'israeli_military_in_syria_the_gaza_strip' => [
			'en' => 'Israeli Military in Syria & the Gaza Strip',
			'ar' => 'الجيش الإسرائيلي في سوريا وقطاع غزة',
			'he' => 'צבא ישראלי בסוריה וברצועת עזה',
		],
		'israeli_military_in_the_gaza_strip' => [
			'en' => 'Israeli Military in the Gaza Strip',
			'ar' => 'الجيش الإسرائيلي في قطاع غزة',
			'he' => 'צבא ישראלי ברצועת עזה',
		],
		'palestinian_militants_in_israel' => [
			'en' => 'Palestinian Militants in Israel',
			'ar' => 'الفصائل الفلسطينية في إسرائيل',
			'he' => 'פלגים חמושים פלסטינים בישראל',
		],
		

		// map sidebar

		'civilian_casualty_incidents_in_this_area' => [
			'en' => 'civilian casualty incidents in this area:',
			'ar' => 'ضحايا مدنيين في هذه المنطقة',
			'he' => 'אירועים עם נפגעים אזרחיים באזור זה:',
		],
		'civilian_casualty_incident_in_this_area' => [
			'en' => 'civilian casualty incident in this area:',
			'ar' => 'حادثة ضحية مدني في هذه المنطقة',
			'he' => 'אירוע עם נפגעים אזרחיים באזור זה:',
		],
		'heading_code' => [
			'en' => 'Code',
			'ar' => 'الرقم',
			'he' => 'קוד אירוע',
		],
		'heading_grading' => [
			'en' => 'Grading',
			'ar' => 'التقييم',
			'he' => 'הערכה',
		],
		'heading_strike_status' => [
			'en' => 'Strike Status',
			'ar' => 'حالة الضربة الجوية',	
			'he' => 'מעמד התקיפה',
		],
		'heading_strike_target' => [
			'en' => 'Strike Target',
			'ar' => 'هدف الضربة الجوية',
			'he' => 'מטרת התקיפה',
		],

		'heading_min_max_civilian_deaths' => [
			'en' => 'Civilian Deaths',
			'ar' => 'الضحايا المدنيين',
			'he' => 'מוות של אזרחים',
		],
		'heading_min_max_civilians_injured' => [
			'en' => 'Civilians Injured',
			'ar' => 'جرحى مدنيين',
			'he' => 'אזרחים שנפצעו',
		],
		'heading_min_max_militant_deaths' => [
			'en' => 'Militant Deaths',
			'ar' => 'القتلى المسلحون',
			'he' => 'מוות של חמושים',
		],
		'heading_min_max_deaths' => [
			'en' => 'Min-Max Deaths',
			'ar' => 'العدد الأقل و الأكثر للقتلى',
			'he' => 'מינימום-מקסימום הרוגים',
		],
		'heading_date' => [
			'en' => 'Date',
			'ar' => 'التاريخ',
			'he' => 'תַאֲרִיך',
		],
		'civilian_casualty_reports_monitored_assessed_and_published' => [
			'en' => 'Civilian casualty reports monitored, assessed and published.',
			'ar' => 'تقارير الضحايا المدنيين التي تم رصدها و تقييمها',
			'he' => 'פגיעות באזרחים שדווחו, נבדקו, הוערכו ופורסמו.',
		],
		'civilian_casualty_reports_monitored_but_not_yet_assessed' => [
			'en' => 'Civilian casualty reports monitored but not yet assessed.',
			'ar' => 'تقارير الضحايا المدنيين التي تم رصدها ولكن لم يتم تقييمها بعد',
			'he' => 'פגיעות באזרחים שדווחו ונבדקו אך טרם הוערכו. ',
		],
		'duration_of_conflict' => [
			'en' => 'Duration of conflict',
			'ar' => 'مدة النزاع',
			'he' => 'משך המערכה',
		],
		



		'map_desc_civilian_fatalities' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for civilian fatalities from US Forces actions.',
			'ar' => 'توضح هذه الخريطة الحد الأدنى لتقديرات Airwars للقتلى المدنيين من عمليات القوات الأمريكية.',
		],
		'map_desc_militant_fatalities' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for militant fatalities from US Forces actions.',
			'ar' => 'توضح هذه الخريطة الحد الأدنى لتقديرات Airwars للقتلى المسلحين من عمليات القوات الأمريكية.',
		],
		'map_desc_strike_locations' => [
			'en' => 'This map shows all strike locations tracked by Airwars, coloured blue if the strike has been declared by US Forces.',
			'ar' => 'تُظهر هذه الخريطة جميع مواقع الضربات التي  تبعتها Airwars، ملونة بالأزرق في حال أعلنت القوات الأمريكية عن الضربة.',
		],
		'map_desc_strike_target' => [
			'en' => 'This map shows all strike locations tracked by Airwars, colour coded by the targeted belligerent.',
			'ar' => 'تُظهر هذه الخريطة جميع مواقع الضربات التي تم تعقبها Airwars، وتم ترميزها بالألوان للمقاتلين المستهدفين.',
		],
		'map_desc_belligerent' => [
			'en' => 'This map shows all strike locations by belligerent.',
			'ar' => 'تُظهر هذه الخريطة جميع مواقع الضربات من قبل مختلف المتحاربين.',
			'he' => 'מפה זו מציגה את כלל מיקומי התקיפות הצבאיות',
		],
		

		'map_desc_civilian_fatalities_all_belligerents_in_libya' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for civilian fatalities from all belligerent actions since 2012.',
			'ar' => 'توضح هذه الخريطة أدنى تقديرات Airwars للقتلى المدنيين من جميع الأعمال القتالية منذ عام 2012.',
		],
		'map_desc_militant_fatalities_all_belligerents_in_libya' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for militant fatalities from all belligerent actions since 2012.',
			'ar' => 'توضح هذه الخريطة أدنى تقديرات Airwars للقتلى المتحاربين من جميع الأعمال القتالية منذ عام 2012.',
		],
		'map_desc_strikes_by_belligerent_all_belligerents_in_libya' => [
			'en' => 'Strikes by all belligerents in Libya',
			'ar' => 'الضربات حسب كل المتحاربين في ليبيا',
		],
		'map_desc_2012_present' => [
			'en' => '2012 – Present',
			'ar' => '2012 حتى الآن',
		],



		'map_desc_civilian_fatalities_us_forces_in_somalia' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for civilian fatalities from US Forces actions.',
			'ar' => 'توضح هذه الخريطة الحد الأدنى لتقديرات Airwars للقتلى المدنيين من عمليات القوات الأمريكية.',
		],
		'map_desc_militant_fatalities_us_forces_in_somalia' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for militant fatalities from US Forces actions.',
			'ar' => 'توضح هذه الخريطة الحد الأدنى لتقديرات Airwars للقتلى المسلحين من عمليات القوات الأمريكية.',
		],
		'map_desc_strikes_by_belligerent_us_forces_in_somalia' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for militant fatalities from US Forces actions.',
			'ar' => 'توضح هذه الخريطة الحد الأدنى لتقديرات Airwars للقتلى المسلحين من عمليات القوات الأمريكية.',
		],


		'map_desc_civilian_fatalities_us_forces_in_yemen_trump' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for civilian fatalities from US Forces actions.',
			'ar' => 'توضح هذه الخريطة الحد الأدنى لتقديرات Airwars للقتلى المدنيين من عمليات القوات الأمريكية.',
		],
		'map_desc_militant_fatalities_us_forces_in_yemen_trump' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for militant fatalities from US Forces actions.',
			'ar' => 'توضح هذه الخريطة الحد الأدنى لتقديرات Airwars للقتلى المسلحين من عمليات القوات الأمريكية.',
		],
		'map_desc_strikes_by_belligerent_us_forces_in_yemen_trump' => [
			'en' => 'This heatmap reports Airwars’ minimum estimate for militant fatalities from US Forces actions.',
			'ar' => 'توضح هذه الخريطة الحد الأدنى لتقديرات Airwars للقتلى المسلحين من عمليات القوات الأمريكية.',
		],




		'map_switch_strikes' => [
			'en' => 'Strikes by Belligerent',
			'ar' => 'الضربات حسب الجهة المتحاربة',
			'he' => 'תקיפות של כוחות צבאיים',
		],
		'map_switch_civilian_fatalities' => [
			'en' => 'Civilian Fatalities',
			'ar' => 'القتلى المدنيون',
			'he' => 'אזרחים שנהרגו',
		],
		'map_switch_militant_fatalities' => [
			'en' => 'Militant Fatalities',
			'ar' => 'القتلى المسلحون',
			'he' => 'חמושים שנהרגו',
		],
		'map_switch_strike_locations' => [
			'en' => 'Strike Locations',
			'ar' => 'مواقع الضربات',
			'he' => 'מיקומי התקיפות',
		],
		'map_switch_strike_target' => [
			'en' => 'Strike Target',
			'ar' => 'هدف الضربة',
			'he' => 'מטרת התקיפה',
		],
		'map_switch_strikes_by_belligerent' => [
			'en' => 'Airstrikes by Belligerent',
			'ar' => 'الضربات الجوية حسب الجهة المتحاربة',
			'he' => 'תקיפות אוויריות של כוחות צבאיים',
		],

		'declared' => [
			'en' => 'Declared',
			'ar' => 'معلنة',
		],
		'alleged' => [
			'en' => 'Alleged',
			'ar' => 'مزعومة',
		],

		'declared_strike' => [
			'en' => 'Declared Strike',
			'ar' => 'ضربة معلنة',
		],
		'alleged_strike' => [
			'en' => 'Alleged Strike',
			'ar' => 'ضربة مزعومة',
		],

		'legend' => [
			'en' => 'Legend:',
			'ar' => 'عنوان تفسيري',
			'he' => 'מקרא:',
		],
		'administrative_boundaries_via_ocha' => [
			'en' => 'Administrative Boundaries, via OCHA',
			'ar' => 'الحدود الإدارية ، عبر مكتب الأمم المتحدة لتنسيق الشؤون الإنسانية',
		],
		'strike_target_al_qaeda_in_the_arabian_peninsula_aqap' => [
			'en' => 'Al Qaeda in the Arabian Peninsula (AQAP)',
			'ar' => 'تنظيم القاعدة في جزيرة العرب',
		],
		'strike_target_isis_yemen' => [
			'en' => 'ISIS - Yemen',
			'ar' => 'داعش في اليمن',
		],


		'strike_target_al_qaeda_in_east_africa' => [
			'en' => 'Al Qaeda in East Africa',
			'ar' => '',
		],
		'strike_target_al_shabaab' => [
			'en' => 'Al-Shabaab',
			'ar' => '',
		],
		'strike_target_isis_somalia' => [
			'en' => 'ISIS - Somalia',
			'ar' => '',
		],


		'strike_target_other' => [
			'en' => 'Other',
			'ar' => 'آخر',
		],
		'strike_target_unknown' => [
			'en' => 'Unknown',
			'ar' => 'مجهول',
		],
		'incident' => [
			'en' => 'incident',
			'ar' => 'حادثة',
			'he' => 'תַקרִית',
		],
		'in_2002' => [
			'en' => 'in 2002',
			'ar' => ' واحدة في 2002',
		],

		'since_january_20_2017' => [
			'en' => 'since January 20, 2017',
			'ar' => 'منذ 20 من يناير 2017',
		],
		'since_2002' => [
			'en' => 'since 2002',
			'ar' => 'منذ سنة 2002',
		],
		'since_december_2009' => [
			'en' => 'since December 2009',
			'ar' => 'منذ ديسمبر 2009',
		],


		'years' => [
			'en' => 'years',
			'ar' => 'سنوات',
		],
		'year' => [
			'en' => 'year',
			'ar' => 'سنة',
		],
		'months' => [
			'en' => 'months',
			'ar' => 'أشهر',
		],
		'month' => [
			'en' => 'month',
			'ar' => 'شهر',
		],
		'days' => [
			'en' => 'days',
			'ar' => 'أيام',
			'he' => 'ימים',
		],
		'day' => [
			'en' => 'day',
			'ar' => 'يوم',
			'he' => 'יְוֹם',
		],




		'unique_reference_code' => [
			'en' => 'Unique Reference Code',
			'ar' => '',
		],
		'permalink' => [
			'en' => 'Permalink',
			'ar' => '',
		],
		'country' => [
			'en' => 'Country',
			'ar' => '',
		],
		'date' => [
			'en' => 'Date',
			'ar' => '',
		],
		'grading' => [
			'en' => 'Grading',
			'ar' => '',
		],
		'latitude' => [
			'en' => 'Latitude',
			'ar' => '',
		],
		'longitude' => [
			'en' => 'Longitude',
			'ar' => '',
		],
		'strike_status' => [
			'en' => 'Strike Status',
			'ar' => '',
		],
		'geolocation_accuracy' => [
			'en' => 'Geolocation Accuracy',
			'ar' => '',
		],
		'civilians_killed_min' => [
			'en' => 'Civilians Killed Min',
			'ar' => '',
		],
		'civilians_killed_max' => [
			'en' => 'Civilians Killed Max',
			'ar' => '',
		],
		'civilians_injured_min' => [
			'en' => 'Civilians Injured Min',
			'ar' => '',
		],
		'civilians_injured_max' => [
			'en' => 'Civilians Injured Max',
			'ar' => '',
		],
		'militants_killed_min' => [
			'en' => 'Militants Killed Min',
			'ar' => '',
		],
		'militants_killed_max' => [
			'en' => 'Militants Killed Max',
			'ar' => '',
		],
		'belligerent_list' => [
			'en' => 'Belligerents List',
			'ar' => '',
		],

		'iraq' => [
			'en' => 'Iraq',
			'ar' => '',
		],
		'syria' => [
			'en' => 'Syria',
			'ar' => 'سوريا',
			'he' => 'סוריה',
		],
		'libya' => [
			'en' => 'Libya',
			'ar' => '',
		],
		'turkey' => [
			'en' => 'Turkey',
			'ar' => '',
		],
		'yemen' => [
			'en' => 'Yemen',
			'ar' => '',
		],
		'somalia' => [
			'en' => 'Somalia',
			'ar' => '',
		],
		'pakistan' => [
			'en' => 'Pakistan',
			'ar' => '',
		],
		'israel' => [
			'en' => 'Israel',
			'ar' => 'إسرائيل',
			'he' => 'ישראל',
		],
		'the_gaza_strip' => [
			'en' => 'the Gaza Strip',
			'ar' => 'قطاع غزة',
			'he' => 'רצועת עזה',
		],
		'gaza' => [
			'en' => 'Gaza',
			'ar' => 'غزة',
			'he' => 'עַזָה',
		],


		'province_governorate' => [
			'en' => 'Province/governorate',
			'ar' => 'المقاطعة / المحافظة',
			'he' => 'נפה/מחוז',
		],
		'district' => [
			'en' => 'District',
			'ar' => 'المنطقة',
			'he' => 'אזור',
		],
		'subdistrict' => [
			'en' => 'Subdistrict',
			'ar' => 'المنطقة الفرعية',
			'he' => 'מועצה',
		],
		'city' => [
			'en' => 'City',
			'ar' => 'المدينة',
			'he' => 'עִיר',
		],
		'town' => [
			'en' => 'Town',
			'ar' => 'البلدة',
			'he' => 'עיירה',
		],
		'village' => [
			'en' => 'Village',
			'ar' => 'القرية',
			'he' => 'כְּפָר',
		],
		'neighbourhood_area' => [
			'en' => 'Neighbourhood/area',
			'ar' => 'الحي/ المنطقة',
			'he' => 'שכונה/אזור',
		],
		'street' => [
			'en' => 'Street',
			'ar' => 'الشارع',
			'he' => 'רְחוֹב',
		],
		'nearby_landmark' => [
			'en' => 'Nearby landmark',
			'ar' => 'معلَم قريب',
			'he' => 'ציון דרך בקרבת מקום',
		],
		'within_100m_via_coalition' => [
			'en' => 'Within 100m (via Coalition)',
			'ar' => 'ضمن مئة متر (حسب التحالف)',
			'he' => 'בטווח של 100 מ\' (מפירסומי הקואליציה)',
		],
		'within_1m_via_coalition' => [
			'en' => 'Within 1m (via Coalition)',
			'ar' => 'ضمن متر واحد (حسب التحالف)',
			'he' => 'בטווח 1 מטר (לשיטת קואליציה)',
		],
		'exact_location_via_airwars' => [
			'en' => 'Exact location (via Airwars)',
			'ar' => 'الموقع المحدد (حسب Airwars)',
			'he' => 'מיקום מדויק (דרך איירוורס)',
		],
		'exact_location_via_coalition' => [
			'en' => 'Exact location (via Coalition)',
			'ar' => 'الموقع المحدد (حسب التحالف)',
			'he' => 'מיקום מדויק (באמצעות קואליציה)',
		],
		'exact_location_other' => [
			'en' => 'Exact location (other)',
			'ar' => 'الموقع المحدد (آخر)',
			'he' => 'מיקום מדויק (אחר)',
		],



		'num_strikes' => [
			'en' => 'num strikes',
			'ar' => 'عدد الضربات',
			'he' => 'מספר התקיפות',
		],
		'strikes' => [
			'en' => 'Strikes',
			'ar' => 'الضربات',
			'he' => 'תקיפות',
		],
		'strike' => [
			'en' => 'Strike',
			'ar' => 'الضربة',
			'he' => 'תקיפה',
		],
		'strike_details' => [
			'en' => 'strike details',
			'ar' => 'تفاصيل الضربة',
			'he' => 'פרטי התקיפה',
		],
		'location_details' => [
			'en' => 'location details',
			'ar' => 'تفاصيل الموقع',
			'he' => 'מידע אודות המיקום',
		],
		'accurate_to' => [
			'en' => 'accurate to',
			'ar' => 'دقيقة حتى',
			'he' => 'מדויק ב',
		],
		'type_of_strike' => [
			'en' => 'Type of strike',
			'ar' => 'نوع الضربة',
			'he' => 'סוג התקיפה',
		],


		'militant_fatality_incidents_in_this_area' => [
			'en' => 'militant fatality incidents in this area',
			'ar' => 'حوادث قتل مسلحين في هذه المنطقة',
			'he' => 'אירועים עם הרוגים מכוחות צבאיים באזור זה:',
		],
		'militant_fatality_incident_in_this_area' => [
			'en' => 'militant fatality incident in this area',
			'ar' => 'حادث قتل مسلحين في هذه المنطقة',
			'he' => 'אירוע עם הרוגים מכוחות צבאיים באזורי זה:',
		],
		'strikes_in_this_area' => [
			'en' => 'strikes in this area',
			'ar' => 'الضربات في هذه المنطقة',
			'he' => 'תקיפות באזור זה',
		],
		'strike_in_this_area' => [
			'en' => 'strike in this area',
			'ar' => 'الضربة في هذه المنطقة',
			'he' => 'תקיפה באזור זה',
		],
		'strikes_with_a_known_target_in_this_area' => [
			'en' => 'strikes with a known target in this area',
			'ar' => 'الضربات مع هدف معلوم في هذه المنطقة',
			'he' => 'תקיפות עם מטרה ידועה באזור זה',
		],
		'strike_with_a_known_target_in_this_area' => [
			'en' => 'strike with a known target in this area',
			'ar' => 'الضربة مع هدف معلوم في هذه المنطقة',
			'he' => 'תקיפה עם מטרה ידועה באזור זה',
		],
		'strike_events_in_this_area' => [
			'en' => 'strike events in this area',
			'ar' => 'حوادث الضربة في هذه المنطقة',
			'he' => 'תקיפות באזור זה',
		],
		'strike_event_in_this_area' => [
			'en' => 'strike event in this area',
			'ar' => 'حدث الضربة في هذه المنطقة',
			'he' => 'תקיפה באזור זה',
		],


		'airstrike_plane' => [
			'en' => 'Airstrike: plane',
			'ar' => 'الضربة: طائرة',
			'he' => 'תקיפה אווירית: מטוס',
		],
		'airstrike_drone' => [
			'en' => 'Airstrike: drone',
			'ar' => 'الضربة: طائرة مسيرة',
			'he' => 'תקיפה אווירית: מזל"ט',
		],
		'airstrike_plane_or_artillery' => [
			'en' => 'Airstrike: plane or artillery',
			'ar' => 'الضربة: طائرة أو مدفعية',
			'he' => 'תקיפה אווירית: מטוס או ארטילריה',
		],
		'airstrike_plane_and_artillery' => [
			'en' => 'Airstrike: plane and artillery',
			'ar' => 'الضربة: طائرة و مدفعية',
			'he' => 'תקיפה אווירית: מטוס וארטילריה',
		],
		'airstrike_helicopter' => [
			'en' => 'Airstrike: helicopter',
			'ar' => 'الضربة: طائرة مروحية',
			'he' => 'תקיפה אווירית: מסוק',
		],
		'airstrike_plane_and_helicopter' => [
			'en' => 'Airstrike: plane and helicopter',
			'ar' => 'الضربة: طائرة وطائرة مروحية',
			'he' => 'תקיפה אווירית: מטוס ומסוק',
		],
		'airstrike_artillery' => [
			'en' => 'Airstrike: artillery',
			'ar' => 'الضربة: مدفعية',
			'he' => 'תקיפה אווירית: ארטילריה',
		],
		'airstrike_unknown' => [
			'en' => 'Airstrike: unknown',
			'ar' => 'الضربة: مجهول',
			'he' => 'תקיפה אווירית: לא ידוע',
		],
		'airstrike_plane_and_drone' => [
			'en' => 'Airstrike: plane and drone',
			'ar' => 'الضربة:  طائرة و طائرة مسيرة',
			'he' => 'תקיפה אווירית: מטוס ומזל"ט',
		],
		'airstrike_plane_or_drone' => [
			'en' => 'Airstrike: plane or drone',
			'ar' => 'الضربة:  طائرة أو طائرة مسيرة',
			'he' => 'תקיפה אווירית: מטוס או מזל"ט',
		],
		'airstrike_drone_and_artillery' => [
			'en' => 'Airstrike: drone and artillery',
			'ar' => 'الضربة: طائرة ومدفعية',
			'he' => 'תקיפה אווירית: מזל"ט וארטילריה',
		],
		'airstrike_plane_or_helicopter' => [
			'en' => 'Airstrike: plane or helicopter',
			'ar' => 'الضربة: طائرة او طائرة مروحية',
			'he' => 'תקיפה אווירית: מטוס או מסוק',
		],
		'airstrike_other' => [
			'en' => 'Airstrike: other',
			'ar' => 'الضربة: آخر',
			'he' => 'תקיפה אווירית: אחר',
		],
		'airstrike_drone_and_helicopter' => [
			'en' => 'Airstrike: drone and helicopter',
			'ar' => 'الضربة: طائرة مروحية و طائرة مسيرة',
			'he' => 'תקיפה אווירית: מזל"ט ומסוק',
		],
		'airstrike_artillery' => [
			'en' => 'Airstrike: artillery',
			'ar' => 'الضربة: سلاح المدفعية',
			'he' => 'תקיפה אווירית: ארטילריה',
		],
		'airstrike_helicopter' => [
			'en' => 'Airstrike: helicopter',
			'ar' => 'الضربة: طائرة مروحية',
			'he' => 'תקיפה אווירית: מסוק',
		],
		'libya_2011_minimum_civilian_fatalities_per_belligerent' => [
			'en' => 'Libya 2011: Minimum civilian fatalities per belligerent',
			'ar' => 'ليبيا 2011: أدنى تقدير لقتلى المدنيين حسب الطرف المتحارب',
		],
		'libya_2011_minimum_number_of_reported_civilian_deaths_by_belligerent' => [
			'en' => 'Minimum number of reported civilians deaths by belligerent.',
			'ar' => 'أدنى تقدير للقتلى المدنيين المبلغ عنهم حسب الطرف المتحارب',
		],
		'gaddafi_forces' => [
			'en' => 'Gaddafi Forces',
			'ar' => 'قوات القذافي',
		],
		'nato_forces' => [
			'en' => 'NATO Forces',
			'ar' => 'قوات الناتو',
		],
		'libyan_rebel_forces' => [
			'en' => 'Libyan Rebel Forces',
			'ar' => 'قوات الثوار',
		],
		'unknown' => [
			'en' => 'Unknown',
			'ar' => 'غير معروف',
		],

		'conflict_events_in_focus' => [
			'en' => 'Conflict Events in Focus',
			'ar' => 'أحداث الصراع تحت الضوء',
		],


		'civilians_casualties_in_the_gaza_strip' => [
			'en' => 'Civilian Casualties in the Gaza Strip',
			'ar' => 'الضحايا المدنيين في قطاع غزة',
			'he' => 'נפגעים אזרחים ברצועת עזה',
		],
		'killed' => [
			'en' => 'Killed',
			'ar' => 'القتلى',
			'he' => 'נהרג',
		],
		'injured' => [
			'en' => 'Injured',
			'ar' => 'الجرحى',
			'he' => 'נִפגָע',
		],
		'incidents' => [
			'en' => 'Incidents',
			'ar' => 'الحوادث',
			'he' => 'תקריות',
		],
		'neighbourhood' => [
			'en' => 'Neighbourhood',
			'ar' => 'حيّ',
			'he' => 'שְׁכוּנָה',
		],
		'click_for_more_information' => [
			'en' => 'Click for more information',
			'ar' => 'أضغط لمزيد من المعلومات',
			'he' => 'לחץ למידע נוסף',
		],

		'click_to_interact' => [
			'en' => 'Click to interact',
			'ar' => 'اضغط للتفاعل',
			'he' => 'לחץ להתחלת חיפוש',
		],


		'english' => [
			'en' => 'English',
			'ar' => 'الأنجليزية',
			'he' => 'אנגלית',
			'uk' => 'Англійська',
		],
		'arabic' => [
			'en' => 'Arabic',
			'ar' => 'العربية',
			'he' => 'ערבית',
			'uk' => 'Арабська',
		],
		'hebrew' => [
			'en' => 'Hebrew',
			'ar' => 'العبرية',
			'he' => 'עברית',
			'uk' => 'Іврит',
		],
		'ukrainian' => [
			'en' => 'Ukrainian',
			'ar' => 'العبرية',
			'he' => 'עברית',
			'uk' => 'Українська',
		],

		'get_gaza_neighbourhood_map_intro_1' => [
			'en' => 'This interactive map illustrates the devastating civilian toll of the May 2021 war in Gaza.',
			'ar' => 'توضح هذه الخريطة التفاعلية الخسائر المدنية الكبيرة في حرب مايو / أيار ٢٠٢١ في غزة',
			'he' => 'מפה אינטראקטיבית זו ממחישה את המחיר האזרחי ההרסני של מלחמת מאי 2021 בעזה.',
		],
		'get_gaza_neighbourhood_map_intro_2' => [
			'en' => 'For 11 days Israel and Palestinian militant groups fought a fierce conflict. Two million Palestinians were trapped in Gaza as <span>1,500</span> strikes hit a territory a quarter the size of greater London.',
			'ar' => 'لمدة 11 يوما ، خاضت الفصائل الفلسطينية و القوات الإسرائيلية صراعا ضاريا. حوصر مليوني فلسطيني في غزة حيث ضربت <span>1,500</span> غارة منطقة تبلغ مساحتها ربع مساحة لندن الكبرى.',
			'he' => 'במשך 11 ימים נלחמו ישראל ופלגים חמושים פלסטינים בסכסוך עז. שני מיליון פלסטינים נלכדו בעזה כאשר 1,500 תקיפות פגעו בשטח שגודלו רבע בגודל של לונדון רבתי.',
		],
		'explore_the_map' => [
			'en' => 'Explore the map',
			'ar' => 'تصّفح الخريطة',
			'he' => 'למפה האינטרקטיבית',
		],

		'neighbourhood_height_civcas_legend' => [
			'en' => 'Neighbourhood height corresponds to minimum number of civilian fatalities.',
			'ar' => 'ارتفاع مؤشر الطبقات على الحي يتوافق مع الحد الأدنى لعدد القتلى المدنيين.',
			'he' => 'גובה השכונה תואם למספר המינימלי של הרוגים אזרחיים.',
		],
		'civilian_casualties_in' => [
			'en' => 'Civilian Casualties in',
			'ar' => 'الضحايا المدنيين في',
			'he' => 'נפגעים אזרחיים ב',
		],
		'view_actions_from_palestinian_militants_in_israel_over_the_same_period' => [
			'en' => 'View actions from <span>Palestinian Militans</span> in Israel over the same period',
			'ar' => 'عرض عمليات <span>الفصائل الفلسطينية في اسرائيل</span> خلال ذات المدة',
			'he' => 'צפה בפעולות של <span>פלגים חמושים פלסטינים</span> בישראל באותה התקופה',
		],
		'gaza_may_10_20_2021' => [
			'en' => 'May 10th–20th 2021',
			'ar' => 'من العاشر إلى العشرين من أيار / مايو ٢٠٢١',
			'he' => '10–20 במאי 2021',
		],
		'loading' => [
			'en' => 'loading',
			'ar' => 'جاري التحميل',
			'he' => 'בטעינה',
		],
		'civilian_killed_injured_incident' => [
			'en' => '{num_killed} civilian killed and {num_injured} injured in {num_incidents} incident',
			'ar' => '{num_killed} من المدنيين قتلوا و جرح {num_injured} اّخرين في {num_incidents} من الحوادث',
			'he' => 'אזרח {num_killed} נהרג ו-{num_injured} נפצע בתקרית {num_incidents}',
		],
		'civilian_killed_injured_incidents' => [
			'en' => '{num_killed} civilian killed and {num_injured} injured in {num_incidents} incidents',
			'ar' => '{num_killed} من المدنيين قتلوا و جرح {num_injured} اّخرين في {num_incidents} من الحوادث',
			'he' => 'אזרח {num_killed} נהרג ו-{num_injured} נפצע ב-{num_incidents} תקריות',
		],
		'civilians_killed_injured_incident' => [
			'en' => '{num_killed} civilians killed and {num_injured} injured in {num_incidents} incident',
			'ar' => '{num_killed} من المدنيين قتلوا و جرح {num_injured} أخرين في {num_incidents} من الحوادث',
			'he' => '{num_killed} אזרחים נהרגו ו-{num_injured} נפצע בתקרית {num_incidents}',
		],
		'civilians_killed_injured_incidents' => [
			'en' => '{num_killed} civilians killed and {num_injured} injured in {num_incidents} incidents',
			'ar' => '{num_killed} من المدنيين قتلوا و {num_injured} أخرين جرحوا في {num_incidents} من الحوادث',
			'he' => '{num_killed} אזרחים נהרגו ו-{num_injured} נפצע ב-{num_incidents} תקריות',
		],



		'gaza_graph_age_title' => [
			'en' => 'Number of unique sources Airwars identified per assessment in Gaza and Israel May 2021, and Syria 2013-2021',
			'ar' => 'عدد المصادر الفريدة التي قامت الحروب الجوية بجمعها لكل تقييم في قطاع غزة وإسرائيل خلال شهر مايو / أيار ٢٠٢١ ، وفي سوريا في الفترة ما بين ٢٠١٣ و ٢٠٢١',
			'he' => 'מספר המקורות הייחודים שאייראורס זיהתה לכל הערכה בעזה ובישראל במאי 2021 ובסוריה בין השנים 2013-2021.',
		],
		'gaza_graph_age_content' => [
			'en' => 'This presents each civilian harm assessment according to the number of unique local sources Airwars has identified, where Israel or Palestinian factions were alleged responsible. Clustering shows both more civilian harm events and more unique sources per incident in Gaza in May 2021 compared to the other theatres. The number of unique sources per civilian harm event across all arenas clusters between 10 and 40, while Gaza exceptionally had several incidents with 100 or more sources.',
			'ar' => ': يعرض هذا تقييم كل حادثة سقوط ضحايا مدنيين وفقًا لعدد من المصادر المحلية الفريدة التي حددتها الحروب الجوية ، حيث زُعم أن إسرائيل أو الفصائل الفلسطينية مسؤولة. يُظهر تجميع المزيد من حوادث سقوط ضحايا مدنيين والمزيد من المصادر الفريدة لكل حادثة في قطاع غزة في مايو / أيار ٢٠٢١ مقارنة بمسارح الصراع الأخرى. عدد المصادر الفريدة لكل حادثة سقوط ضحايا مدنيين في جميع مجموعات ساحات الصراع بين ١٠ و ٤٠ ، في حين أن قطاع غزة شهد بشكل استثنائي عدة حوادث تم أرشفة فيها ١٠٠ مصدر أو أكثر.',
			'he' => '"תרשים זה מציג כל אומדן של פגיעה באזרחים על פי מספר המקורות המקומיים הייחודיים שזוהו על ידי Airwars, בהם ישראל או פלגים פלסטינים הוחזקו בגדר אחראיים לכאורה. המקבצים מראים יותר אירועי פגיעה באזרחים ויותר מקורות ייחודיים לכל תקרית בעזה במאי 2021, בהשוואה לזירות האחרות. מספר המקורות הייחודיים לכל אירוע של פגיעה באזרחים בכל הזירות עומד בין 10 ל 40, בעוד שבעזה ישנן כמה תקריות אשר להן למעלה מ 100 מקורות.',
		],


		'gaza_graph_deaths_title' => [
			'en' => 'Minimum reported civilian deaths by likely Israeli action in Gaza, May 2021, by time of day',
			'ar' => 'الضحايا المدنيين المُبلغ عنهم في قطاع غزة نتيجة للقصف الذي يُدعى أنه إسرائيلي مايو / أيار ٢٠٢١ خلال التوقيت من اليوم',
			'he' => 'מינימום מקרי מוות אזרחיים כתוצאה מפגיעה המיוחסת לישראל בעזה, מאי 2021, לפי שעה ביום',
		],
		'gaza_graph_deaths_content' => [
			'en' => 'This stacks the minimum number of likely civilian deaths according to reported demographics by time of day. Most civilian casualties were reported at night.',
			'ar' => '"هذا يشير إلى الحد الأدنى لعدد الوفيات المحتملة بين المدنيين وفقًا للتركيبة السكانية المبلغ عنها محليا حسب الوقت من اليوم. تم الإبلاغ عن معظم الضحايا المدنيين في الليل.',
			'he' => 'סיכום המספר המינימאלי של מקרי מוות אזרחיים בסבירות גבוהה על בסיס דיווחים דמוגרפיים על פי השעה ביום. רוב הנפגעים האזרחיים דווחו בלילה.',
		],


		'gaza_graph_sources_title' => [
			'en' => 'Average age of civilian casualties per harm event, Gaza and Israel May 2021',
			'ar' => 'متوس أعمار الضحايا المدنيين في الحادثة الواحدة ، في قطاع غزة وإسرائيل في مليو / أيار ٢٠٢١',
			'he' => 'גיל ממוצע של נפגעים אזרחיים בכל אירוע, עזה וישראל מאי 2021',
		],
		'gaza_graph_sources_content' => [
			'en' => 'Each data point represents a civilian harm assessment in Gaza or Israel during May 2021, which has been categorised by the average age of all casualties reported. As the graph shows, most civilians harmed in Israel were older persons who in many cases would have been less able to quickly access shelters.',
			'ar' => 'تمثل كل نقطة بيانات تقييمًا للأضرار التي لحقت بالمدنيين في قطاع غزة أو إسرائيل خلال مايو / أيار ٢٠٢١، والتي تم تصنيفها حسب متوسط عمر جميع الضحايا المبلغ عنها. كما يظهر الرسم البياني ، كان معظم المدنيين المتضررين في إسرائيل من كبار السن الذين في كثير من الحالات كانوا أقل قدرة على الوصول بسرعة إلى الملاجئ.',
			'he' => 'כל נקודת נתונים מייצגת הערכת פגיעה באזרחים בעזה או בישראל במאי 2021, וסווגה לפי גיל הממוצע של הנפגעים שדווח. כפי שעולה מהגרף, רוב האזרחים שנפגעו בישראל היו אנשים מבוגרים אשר בכל מקרה לא היו מספיקים להגיע במהירות למקלטים',
		],
	

	];

	return dict_lookup($dictionary, $str, $lang);

}

function dict_lookup($dictionary, $str, $lang) {
	if (isset($dictionary[$str])) {
		if (isset($dictionary[$str][$lang]) && $dictionary[$str][$lang] != '') {
			return $dictionary[$str][$lang];	
		} elseif ($lang == 'ar' && isset($dictionary[$str]['ar_google'])) {
			return $dictionary[$str]['ar_google'];
		}
	}
	if (isset($dictionary[$str]['en'])) {
		return $dictionary[$str]['en'];
	}
	return $str;
	// return '***NO TRANSLATION FOUND*** ' . $str;
}