import React, { useState, useEffect, useMemo, useRef } from 'react';
import { useDebounce } from 'use-debounce';
import Highlighter from 'react-highlight-words';
import { FixedSizeList as List } from 'react-window';
import { saveAs } from 'file-saver';





function useWindowSize() {
	const [size, setSize] = useState({
		width: window.innerWidth < 1400 ? window.innerWidth : 1400,
		height: window.innerHeight,
	});

	useEffect(() => {
		function handleResize() {
			setSize({
				width: window.innerWidth < 1400 ? window.innerWidth : 1400,
				height: window.innerHeight,
			});
		}

		// Add event listener to track window resize
		window.addEventListener('resize', handleResize);

		// Cleanup event listener on unmount
		return () => {
			window.removeEventListener('resize', handleResize);
		};
	}, []);

	return size;
}

export default function App(props) {

	const data = props.data.data.names
	const translations = props.data.data.translations
	const postData = props.data.post_data
	const lang = postData.lang

	const [search, setSearch] = useState('');
	const [ageMin, setAgeMin] = useState(null);
	const [ageMax, setAgeMax] = useState(null);
	const [gender, setGender] = useState('all');
	const [list, setList] = useState('all');

	const [activeTab, setActiveTab] = useState('list') // Default to 'list'

	const [debouncedSearch] = useDebounce(search, 300); // Debounce the search input by 300ms
	const size = useWindowSize();
	const contentRef = useRef(null);
	const tableHeaderRef = useRef(null);
	const tabRef = useRef(null);

	const [contentHeight, setContentHeight] = useState(0); // State for dynamic height

	useEffect(() => {
		if (contentRef.current && activeTab === 'list') {
			const contentRect = contentRef.current.getBoundingClientRect()
			const tableHeaderRect = tableHeaderRef.current.getBoundingClientRect()
			const headerRect = document.querySelector('header').getBoundingClientRect()
			const tabRect = tabRef.current.getBoundingClientRect()
			
			const contentHeightValue = size.height - (contentRect.height + tableHeaderRect.height + headerRect.height + tabRect.height)

			setContentHeight(contentHeightValue);
		}
	}, [size]); // Recalculate when dependencies change

	// Determine row height based on device width (simple responsive logic)
	const itemSize = size.width < 1024 ? 145 : 27;

	// Precompute lowercased fields so we don't have to do this repeatedly
	const preprocessedData = useMemo(() => {
		return data.map(entry => {
			return ({
				...entry,
				nameArabicLower: entry['name-arabic'].toLowerCase(),
				transliterationLower: entry['name-transliterated'].toLowerCase(),
			})
		});
	}, [data]);

	const words = useMemo(() =>
		debouncedSearch
			.split(' ')
			.map(w => w.toLowerCase())
			.filter(word => word.length > 0),
	[debouncedSearch]);

	const filteredData = useMemo(() => {
	
		if (!preprocessedData) return [];

		return preprocessedData.filter(entry => {
			// Combine all searchable fields into a single text string
			const entryText = `${entry.nameArabicLower} ${entry.transliterationLower} ${entry.id}`;

			// Normalize text: remove extra spaces, split into words
			const entryWords = entryText.toLowerCase().split(/\s+/);

			// Count occurrences of each search word in the entry using substring matching
			const wordCounts = words.reduce((acc, word) => {
				acc[word] = entryWords.filter(entryWord => entryWord.includes(word)).length;
				return acc;
			}, {});

			// Ensure each word appears at least as many times as in the search query
			const matchesSearch = words.every(word =>
				wordCounts[word] >= words.filter(w => w === word).length
			);

			// Filter based on age range
			const matchesAge =
				(ageMin === null || (entry.age !== '' && entry.age >= ageMin)) &&
				(ageMax === null || (entry.age !== '' && entry.age <= ageMax));

			// Filter based on gender
			const matchesGender =
				gender === 'all' ||
				entry.sex === gender ||
				(gender === 'not_specified' && !entry.sex);

			// Filter based on list
			const matchesList = list === 'all' || entry.list.includes(list);

			// Apply all filters
			return matchesSearch && matchesAge && matchesGender && matchesList;
		});
	}, [words, preprocessedData, ageMin, ageMax, gender, list]);


	let showTotalNames = (debouncedSearch || gender !== 'all' || list !== 'all' || ageMin || ageMax);


	const handleGenderChange = (event) => {
		setGender(event.target.value);
	};

	const handleListChange = (event) => {
		setList(event.target.value);
	};

	const handleAgeMinChange = (event) => {
		const val = parseInt(event.target.value, 10)
		const value = (!isNaN(val) && val >= 0) ? val : null;
		setAgeMin(value);
	};

	const handleAgeMaxChange = (event) => {
		const val = parseInt(event.target.value, 10)
		const value = (!isNaN(val) && val >= 0) ? val : null;
		setAgeMax(value);
	};


	const isLoggedIn = document.querySelector('body').classList.contains('logged-in')


	const downloadCSV = () => {
		if (filteredData.length === 0) return;

		const csvRows = [];
		// const headers = ['ID', 'Name (Arabic)', 'Transliteration', 'Sex', 'Age', 'Source', 'Lists'];

		const headers = [
			translations.column_heading_national_id[lang],
			translations.column_heading_name_arabic[lang],
			translations.column_heading_name_transliteration[lang],
			translations.filter_sex[lang],
			translations.filter_age[lang],
			translations.column_heading_moh_source[lang],
			translations.column_heading_lists[lang],
		]

		csvRows.push(headers.join(','));

		for (const entry of filteredData) {

			const csvSource = (lang === 'en') ? entry.source : entry.source_ar


			let csvSexLabel = ''
			if (entry.sex === 'M') {
				csvSexLabel = translations.sex_m[lang]
			}
			if (entry.sex === 'F') {
				csvSexLabel = translations.sex_f[lang]
			}


			const row = [
				entry.id,
				`"${entry['name-arabic']}"`,
				`"${entry['name-transliterated']}"`,
				csvSexLabel,
				entry.age || '',
				`"${csvSource}"`,
				`"${entry.list}"`
			];
			csvRows.push(row.join(','));
		}

		// Prepend BOM to ensure Excel correctly detects UTF-8 encoding
		const csvString = `\uFEFF${csvRows.join('\n')}`;
		const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });

		// Use FileSaver's saveAs function to trigger the download
		saveAs(blob, 'moh-names.csv');
	};

	const LanguageSwitcher = () => {
		return <div className="moh__languages">
			{lang == 'en' && 
				<>
					<div>English</div>
					<a className="ar" href="/research/moh-list-ar">العربية</a>
				</>
			}
			{lang == 'ar' && 
				<>
					<a href="/research/moh-list">English</a>
					<div className="ar">العربية</div>
				</>
			}

		</div>
	}

	// Row renderer for react-window
	const Row = ({ index, style }) => {
		const entry = filteredData[index];

		let sexLabel = ''
		if (entry.sex === 'M') {
			sexLabel = translations.sex_m[lang]
		}
		if (entry.sex === 'F') {
			sexLabel = translations.sex_f[lang]
		}

		return (

			<div className="moh__outer" style={style}>
				<div className="moh__row" key={`entry_${index}_${entry.post_id}_${entry.id}`} >
					
					<div className="moh__code">
						<h4>{translations.column_heading_national_id[lang]}</h4>
						<span>{}</span>
						<Highlighter
							highlightClassName="mark"
							searchWords={words}
							autoEscape={true}
							textToHighlight={entry.id}
						/>

					</div>

					<div className="moh__mobilename moh__arabic">
						<h4>{translations.column_heading_name[lang]}</h4>
						<Highlighter
							highlightClassName="mark"
							searchWords={words}
							autoEscape={true}
							textToHighlight={`${entry['name-arabic']}, ${entry['name-transliterated']}`}
						/>
					</div>

					<div className="moh__name moh__arabic">
						<h4>{translations.column_heading_name[lang]}</h4>
						<Highlighter
							highlightClassName="mark"
							searchWords={words}
							autoEscape={true}
							textToHighlight={entry['name-arabic']}
						/>
					</div>
					<div className="moh__name">
						<h4>{translations.column_heading_name_transliteration[lang]}</h4>
						<Highlighter
							highlightClassName="mark"
							searchWords={words}
							autoEscape={true}
							textToHighlight={entry['name-transliterated']}
						/>
					</div>
					<div className="moh__sexage">
						<h4>{translations.column_heading_sex_age[lang]}</h4>
						<span>
							{sexLabel} {entry.age}
						</span>
					</div>
					
					<div className="moh__lists">
						<h4>{translations.column_heading_lists[lang]}</h4>
						{entry['list']}
					</div>
					<div className="moh__source">

						{entry.source && (
							<>
								<h4>{translations.column_heading_moh_source[lang]}</h4>
								<div>
									<div className="moh__filtersmoreinfo hastooltip">
										<span>+</span>
										<div className="tooltip">
											<div className="tooltip__inner">
												<div style={{fontWeight: 500}}>{translations.tooltip_source[lang]}</div>
												<div>{lang === 'en' ? entry.source : entry.source_ar}</div>
											</div>
										</div>
									</div>

									<div className="moh__truncated">{lang === 'en' ? entry.source : entry.source_ar}</div>
								</div>
							</>
						)}
					</div>
					
					{entry.post_id && (entry.post_status === 'publish' || isLoggedIn) && (
						<div className="moh__code moh__airwarscode">
							<h4>{translations.column_heading_incident[lang]}</h4>
							<span>{entry.post_id && <a href={entry.permalink} target="_blank">{entry.code}</a>}</span>
						</div>
					)}
					
				</div>
			</div>
		);
	};


	return (
		<div className="moh">
			{/* Search Input */}
			
			<div className="moh__content" ref={contentRef}>
				<div className="moh__title">
					<h1>{postData.title}</h1>
					<div className="moh__languages">
						{lang == 'en' && 
							<>
								<div>EN</div>
								<a className="ar" href="/research/moh-list-ar">AR</a>
							</>
						}
						{lang == 'ar' && 
							<>
								<a href="/research/moh-list">EN</a>
								<div className="ar">AR</div>
							</>
						}

					</div>

				</div>


				<div className="moh__excerpt">
					{postData.excerpt ? <div>{postData.excerpt}</div> : <div>{translations.description[lang]}</div>}
				</div>
				<div className="moh__mobileexcerpt">
					{lang == 'en' ? <div>A searchable list of named victims in Gaza since October 2023, as published by the Palestinian Health Ministry in Gaza, reproduced by Airwars for research purposes.</div> : <div>"قائمة قابلة للبحث تضم أسماء جميع الضحايا في غزة منذ تشرين الأول (أكتوبر) 2023، وفقًا للبيانات الصادرة عن وزارة الصحة الفلسطينية في غزة، أُعيد نشرها من قبل منظمة (إيرورز) لأغراض بحثية.</div>}
				</div>							
			</div>

			<div ref={tabRef} className="moh__tabcontainer">
				<div className="moh__tabs">
					<div
						className={activeTab === 'list' ? 'active' : ''}
						onClick={() => setActiveTab('list')}
					>
						{translations.tab_names_list[lang]}
					</div>
					<div
						className={activeTab === 'note' ? 'active' : ''}
						onClick={() => setActiveTab('note')}
					>
						{translations.tab_explanatory_note[lang]}
					</div>
					<div
						className={activeTab === 'tips' ? 'active' : ''}
						onClick={() => setActiveTab('tips')}
					>
						{translations.tab_search_tips[lang]}
					</div>
				</div>
				<LanguageSwitcher/>
			</div>

			{activeTab === 'note' && (
				<div className="moh__note">

					<div className="moh__text">	
						<h1>{translations.tab_explanatory_note[lang]}</h1>
						<div dangerouslySetInnerHTML={{ __html: postData.content}}></div>		
					</div>
				</div>
			)}

			{activeTab === 'tips' && postData.additional_content.tips && (
				<div className="moh__text moh__tips">				
					<div>
						<h1>{translations.tab_search_tips[lang]}</h1>
						<div dangerouslySetInnerHTML={{ __html: postData.additional_content.tips.content}}></div>
					</div>
				</div>
			)}


			{activeTab === 'list' && (
				<div className="moh__table">
					

					<div className="moh__tableheader" ref={tableHeaderRef}>
						<div className="moh__searchfilters">
							<div className="moh__search">
								<h4>{translations.filter_search[lang]}</h4>
								<input
									dir="auto"
									type="text"
									placeholder={translations.filter_type_a_name[lang]}
									onChange={e => setSearch(e.target.value)}
									style={{ width: '100%', boxSizing: 'border-box' }}
								/>
							</div>
							
							<div className="moh__sex">
								<h4>{translations.filter_sex[lang]}</h4>
								<div>
									<select name="gender" onChange={handleGenderChange}>
										<option value="all">{translations.filter_all[lang]}</option>
										<option value="M">{translations.filter_male[lang]}</option>
										<option value="F">{translations.filter_female[lang]}</option>
										<option value="not_specified">{translations.filter_not_specified[lang]}</option>
									</select>
								</div>
							</div>
							<div className="moh__listselect">
								<h4>{translations.filter_list[lang]}</h4>
								<div>
									<select name="list" onChange={handleListChange}>
										<option value="all">{translations.filter_all[lang]}</option>
										{[...Array(7).keys()].map(i => <option value={(i + 1)} key={`list_${i + 1}`}>{i + 1}</option>)}
									</select>
								</div>
							</div>
							<div className="moh__age">
								<h4>{translations.filter_age[lang]}</h4>
								<div>
									<input 
										type="number" 
										placeholder={translations.filter_min[lang]} 
										value={(ageMin !== null && ageMin >= 0) ? ageMin : ''} 
										onChange={handleAgeMinChange} 
									/>

									<input 
										type="number" 
										placeholder={translations.filter_max[lang]} 
										value={(ageMax !== null && ageMax >= 0) ? ageMax : ''} 
										onChange={handleAgeMaxChange} 
									/>
								</div>
							</div>
						
						</div>
						<div className="moh__results">						
							{filteredData.length > 0 
								? (
									<>
										{showTotalNames && (
											<div className="moh__namesnumber">
												{filteredData.length.toLocaleString()} {translations.results_names[lang]}
												<div className="hastooltip">

													<i className="fa fa-info-circle" aria-hidden="true"></i>
													<div className="tooltip">
														<div className="tooltip__inner">
															<div>{translations.tooltip_number_of_results[lang]}</div>
														</div>
													</div>
												</div>
												{' '}
											</div>

											
										)}
										<a href="#" onClick={downloadCSV}>{translations.download_csv[lang]}</a>

									</>
								)
								: 'No names found'}
						</div>
						{/* Use react-window for virtualization - fill the remaining space */}
						{filteredData.length > 0 && (
							<div className="moh__header">
								<div className="moh__row">

									

									<div className="moh__filterslabel">
										<div className="moh__filtersmoreinfo hastooltip">
											<i className="fa fa-info-circle" aria-hidden="true"></i>
											<div className="tooltip">
												<div className="tooltip__inner">
													<div>{translations.tooltip_national_id[lang]}</div>
												</div>
											</div>

										</div>
										<div className="moh__headerlabel">{translations.column_heading_national_id[lang]}</div>
									</div>

									<div className="moh__filterslabel">
										<div className="moh__filtersmoreinfo hastooltip">
											<i className="fa fa-info-circle" aria-hidden="true"></i>
											<div className="tooltip">
												<div className="tooltip__inner">
													<div>{translations.tooltip_name_arabic[lang]}</div>
												</div>
											</div>

										</div>
										<div className="moh__headerlabel">{translations.column_heading_name_arabic[lang]}</div>
									</div>

									<div className="moh__filterslabel">
										<div className="moh__filtersmoreinfo hastooltip">
											<i className="fa fa-info-circle" aria-hidden="true"></i>
											<div className="tooltip">
												<div className="tooltip__inner">
													<div>{translations.tooltip_name_transliteration[lang]}</div>
												</div>
											</div>

										</div>
										<div className="moh__headerlabel">{translations.column_heading_name_transliteration[lang]}</div>
									</div>
									<div className="moh__filterslabel">
										<div className="moh__filtersmoreinfo hastooltip">
											<i className="fa fa-info-circle" aria-hidden="true"></i>
											<div className="tooltip">
												<div className="tooltip__inner">
													<div>{translations.tooltip_sex_age[lang]}</div>
												</div>
											</div>

										</div>
										<div className="moh__headerlabel">{translations.column_heading_sex_age[lang]}</div>
									</div>

									
									<div className="moh__filterslabel">
										<div className="moh__filtersmoreinfo hastooltip">
											<i className="fa fa-info-circle" aria-hidden="true"></i>
											<div className="tooltip">
												<div className="tooltip__inner">
													<div>{translations.tooltip_lists[lang]}</div>
												</div>
											</div>

										</div>
										<div className="moh__headerlabel"><a href="https://docs.google.com/spreadsheets/d/1DpyvHQlMAug43wY1ydbeIvIF01rcH4_-aMT9p-AWUVQ/edit?gid=109569791#gid=109569791" target="_">{translations.column_heading_lists[lang]}</a></div>
									</div>
									<div className="moh__filterslabel">
										<div className="moh__filtersmoreinfo hastooltip">
											<i className="fa fa-info-circle" aria-hidden="true"></i>
											<div className="tooltip">
												<div className="tooltip__inner">
													<div>{translations.tooltip_moh_source[lang]}</div>
												</div>
											</div>

										</div>
										<div className="moh__headerlabel">{translations.column_heading_moh_source[lang]}</div>
									</div>
									<div className="moh__filterslabel">
										<div className="moh__filtersmoreinfo hastooltip">
											<i className="fa fa-info-circle" aria-hidden="true"></i>
											<div className="tooltip">
												<div className="tooltip__inner">
													<div>{translations.tooltip_incident[lang]}</div>
												</div>
											</div>

										</div>
										<div className="moh__headerlabel">{translations.column_heading_incident[lang]}</div>
									</div>
								</div>
							</div>
						)}
					</div>

				
					<div className="moh__list">
						<List
							height={Math.round(contentHeight)} // subtract the height of the input area if needed
							itemCount={filteredData.length}
							itemSize={itemSize}
							width={size.width}
							direction={lang === 'ar' ? 'rtl' : 'ltr'}
						>
							{Row}
						</List>
					</div>
				</div>
			)}
		</div>
	);
}
