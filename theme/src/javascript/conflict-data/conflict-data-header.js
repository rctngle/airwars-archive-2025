import React from 'react'

export default function(props) {

	return (
		<div>
			<h1>{props.data.title}</h1>
			<div dangerouslySetInnerHTML={{__html: props.data.content}} />
			

			<div className="langswitcher">

				{props.data.translations.length > 0 && <a href="?lang=en">English</a>}
				{props.data.translations.map(language => {
					return (
						<a key={`lang_${language.value}`} href={`?lang=${language.value}`}>{language.label}</a>
					)
				})}
			</div>
		</div>
	)
}