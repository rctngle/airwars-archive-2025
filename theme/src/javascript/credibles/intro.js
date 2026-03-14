import React from 'react';

function formatNumber(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

export default class Intro extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			lang: 'en',
		};

		this.setLangEnglish = this.setLangEnglish.bind(this);
		this.setLangArabic = this.setLangArabic.bind(this);
	}

	setLangEnglish(e) {
		e.preventDefault();
		this.setState({
			lang: 'en',
		});
	}

	setLangArabic(e) {
		e.preventDefault();
		this.setState({
			lang: 'ar',
		});
	}

	render() {

		// const numIncidents = formatNumber(this.props.numIncidents);
		// const civcasConceded = formatNumber(this.props.civcasConceded);
		// // const civcasConceded = formatNumber(1370);
		// const civcasAirwars = formatNumber(this.props.civcasAirwars);
		// const numNamedVictims = formatNumber(this.props.numNamedVictims);

		const numIncidents = formatNumber(344);
		const civcasConceded = formatNumber(1410);
		const civcasAirwars = formatNumber(2024);
		const numNamedVictims = formatNumber(this.props.numNamedVictims);



		let introText = null;
		let title = null;
		if (this.state.lang === 'en') {
			title = 'The Credibles';
			introText = (
				<div className="left en">
					<p>Following more than three years of extensive engagement with US military officials, Airwars has secured the precise locations of all but four of the <strong>{numIncidents}</strong> publicly confirmed (‘Credible’) Coalition civilian harm events in the war against ISIS in both Iraq and Syria, which between them represent the great majority of at least <strong>{civcasConceded}</strong> conceded civilian deaths according to CJTFOIR - and at least <strong>{civcasAirwars}</strong> fatalities according to Airwars.</p>
					<p>These located events represent the most accurate and comprehensive data ever publicly revealed by the US military about the harm it causes in war. In a significant number of these events, precise cross matching reveals exactly which civilians died, when and where. Given sometimes limited on-the-ground reporting by local communities during the heat of battle, other cases have only been revealed due to US pilots and analysts coming forward and flagging concerns. This new locational data ensures that these events can now be properly investigated.</p>
					<p>Explaining its decision to release the complete data, the Coalition’s former chief spokesman Col. Myles Caggins told Airwars: “We take every allegation of civilian casualties with the utmost sincerity, concern, and diligence; we see the addition of the geolocations as a testament to transparency, and our commitment to working with agencies like Airwars to correctly identify civilian harm incidents.” </p>
					<p>There are substantial implications for the Credibles dataset. For the first time, Iraqis and Syrians can now know which confirmed events their loved ones were, or were not, harmed in. The decision by the Pentagon to release all close locational data also sets new better practice standards for other US conflicts, such as Afghanistan. And the US’s key military partners have a key transparency benchmark to aspire to.</p>
					<p>Use the map and moveable timeline to explore specific events. Locations and victim names can be searched in both Arabic and English.</p>
				</div>
			);
		} else {
			title = 'الحوادث الموثوقة';
			introText = (
				<div className="left ar">
					<p>بعد أكثر من ثلاثة أعوام من الانخراط المكثف مع المسؤولين العسكريين الأمريكيين، تمكنت Airwars من تحديد المواقع الدقيقة ل <strong>{numIncidents}</strong> موقعا من مواقع الحوادث المؤكدة الموثوقة التي أدت لسقوط ضحايا بين المدنيين والتي تمت من قبل قوات التحالف باستثناء أربعة منها فقط خلال الحرب ضد ما يسمى تنظيم الدولة الإسلامية في العراق وسوريا. من ضمن هذه البيانات اعتراف CJTFOIR بمقتل ما لا يقل عن <strong>{civcasConceded}</strong> مدنيا  بينما قامت Airwars بتوثيق مقتل <strong>{civcasAirwars}</strong> مدنيا على الأقل. </p>
					<p>تمثل هذه الحوادث محددة الموقع المعطيات الأكثر دقة وشمولية على الإطلاق التي تم الإفصاح عنها من قبل القوة العسكرية الأمريكية حول حوادث ضحايا مدنيين في الحروب. في عدد لا يستهان به من هذه الحوادث تظهر المقارنة الدقيقة من هم المدنيون الذين قتلوا، أين ومتى. بسبب التغطية المحدودة أحيانا على الأرض من قبل المجتمعات المحلية نظرا لضراوة المعارك، تم الاعتراف بحالات أخرى فقط عن طريق طيارين و محللين أمريكيين أشاروا إليها. تضمن هذه المعطيات الجديدة محددة الموقع امكانية التحقيق في هذه الحوادث.</p>
					<p>وتفسيرا لقرارها تقديم البيانات الكاملة، قال المتحدث السابق باسم قوات التحالف الكولونيل مايلز كاغينز ل Airwars: ”نحن نأخذ كل ادعاء بوقوع إصابات بشرية بمنتهى الاهتمام والقلق. نستطيع أن نرى الإضافة التي يقدمها تحديد المواقع كشهادة لشفافيتنا والتزامنا بالعمل مع منظمات مثل Airwars لنحدد بشكل صحيح الحوادث التي الحقت  الأذى بالمدنيين.“</p>
					<p>هنالك نتائج مهمة لمجموعة البيانات المتعلقة بالحوادث الموثوقة. للمرة الأولى على الإطلاق يستطيع العراقيون والسوريون الآن معرفة الحوادث التي تأذى فيها أحباؤهم. إن قرار البنتاغون الكشف عن كل المعلومات الدقيقة المتضمنة للموقع يمثل أفضل الإجراءات في صراعات الولايات المتحدة الأخرى مثل أفغانستان، ويشكل مثال شفافية أساسيا لشركائها العسكريين الأساسيين للاحتذاء به.</p>
					<p>يمكنكم استخدام الخريطة والجدول الزمني لاستكشاف حوادث بعينها، كما تستطيعون البحث عن مواقع الحوادث وأسماء الضحايا باللغتين العربية والإنجليزية على السواء.</p>
				</div>
			);
		}

		return (
			<div className="full-intro">
				<div className="full">
					<div className="title-language">
						<h1 className={this.state.lang}>{title}</h1>	
						<div className="lang-switcher">
							<a href="en" className={(this.state.lang === 'en') ? 'active' : ''} onClick={this.setLangEnglish}>EN</a> 
							<a href="ar" className={(this.state.lang === 'ar') ? 'active' : ''} onClick={this.setLangArabic}>عربي</a>
						</div>
					</div>
					{introText}
					<div className="right">
						<div className="stat">
							<div className="value">{numIncidents}</div>
							<div className="label">publicly confirmed Coalition civilian harm events</div>
						</div>
						<div className="stat">
							<div className="value">{civcasConceded}</div>
							<div className="label">civilian deaths according to CJTFOIR</div>
						</div>
						<div className="stat">
							<div className="value">{civcasAirwars}</div>
							<div className="label">civilian deaths according to Airwars</div>
						</div>
						<div className="stat named">
							<div className="value">{numNamedVictims}</div>
							<div className="label">named civilian casualties</div>
						</div>
					</div>
				</div>
			</div>
		);
	}
}