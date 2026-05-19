<?php

/**
 * Canonical scripted replies from `Bansal_Immigration_Chatbot_Training.docx`:
 * Sections 3 (Q&A), key Section 6 one-liners where helpful for paraphrases.
 * Punjabi/Hindi bilingual line in Word is unreadable after XML extract; stored answer is the
 * authorised English wording given in parentheses in the training document.
 */
return [
    // ── Short greeting / openings (Section 6) ───────────────────────────────
    [
        'category' => '6 Quick templates',
        'sort_order' => 1,
        'question' => 'Greeting (short hello)',
        'answer' => 'Hi! Thank you for contacting Bansal Immigration Consultants. How can we assist you today?',
        'match_signals' => '^hi\b|^hello\b|^hey\b|^good morning\b|^good afternoon\b|^good evening\b|^namaste\b|^sat sri akaal\b',
    ],
    [
        'category' => '6 Quick templates',
        'sort_order' => 2,
        'question' => 'Existing client enquiry',
        'answer' => 'For updates on existing applications, please contact our office at info@bansalimmigration.com.au or call directly. Would you like the contact details?',
        'match_signals' => 'existing client|already a client|my application status| lodged already| lodged my|application progress|case lodged',
    ],
    [
        'category' => '6 Quick templates',
        'sort_order' => 3,
        'question' => 'Angry or distressed user',
        'answer' => 'I understand your concern. Let me have one of our consultants contact you personally to assist further. Please share your name and phone number.',
        'match_signals' => 'angry|fed up|stressed out|panic|panicking|crying|depressed|mental health|cant handle|hopeless',
    ],

    // ── 3.1 General ───────────────────────────────────────────────────────────
    [
        'category' => '3.1 General Enquiries',
        'sort_order' => 101,
        'question' => 'What is the first step to get Australian PR?',
        'answer' => 'We assist with several PR pathways including skilled migration, employer sponsorship, and partner visas. Our MARA-registered consultants can walk you through the right path for your situation — would you like to book a free consultation?',
        'match_signals' => 'first step.*pr|\bhow do i\b.*\b(pr|permanent residency)\b|get australian pr|pathway.*pr\b',
    ],
    [
        'category' => '3.1 General Enquiries',
        'sort_order' => 102,
        'question' => 'Do you guarantee visa approval?',
        'answer' => 'Visa outcomes are decided by the Department of Home Affairs — no registered consultant can legally guarantee results. Our team guides you professionally every step of the way. Shall we book a consultation?',
        'match_signals' => 'guarantee|100% approval|sure i will get|visa success guarantee',
    ],
    [
        'category' => '3.1 General Enquiries',
        'sort_order' => 103,
        'question' => 'Where are your offices?',
        'answer' => 'We have offices in Melbourne (Level 8/278 Collins St) and Adelaide (Unit 5/55 Gawler Pl), open Monday to Friday 9:30am–6:00pm. Would you like to book a consultation at your preferred location?',
        'match_signals' => 'where.*office|\boffice\b.*\b(where|located|melbourne|adelaide)|what offices|locations?\b|\bbranch\b|\bvisit you\b|\bmeet you\b',
    ],
    [
        'category' => '3.1 General Enquiries',
        'sort_order' => 104,
        'question' => 'How much does a consultation cost?',
        'answer' => 'Free 10-minute consultations are available for clients inside Australia. Complex matters or overseas clients may require a $150 paid consultation. Would you like to book one now?',
        'match_signals' => 'consultation cost|how much.*consult|\bfee\b.*consult|charge.*appointment|appointment.*cost',
    ],
    [
        'category' => '3.1 General Enquiries',
        'sort_order' => 105,
        'question' => 'Are your agents MARA registered?',
        'answer' => 'Yes, our consultants are MARA-registered migration agents — fully qualified and regulated. Would you like to speak with one of them today?',
        'match_signals' => 'mara|registered agent|migration agent registered|regulated agent',
    ],
    [
        'category' => '3.1 General Enquiries',
        'sort_order' => 106,
        'question' => 'How long will my visa take to process?',
        'answer' => 'Processing times vary depending on the visa type and your individual circumstances — I\'m unable to provide estimates here. Our consultants can discuss this with you in detail. Shall we book a consultation?',
        'match_signals' => 'how long\b.*visa|processing time|visa take to process|visa timeline|visa wait time',
    ],
    [
        'category' => '3.1 General Enquiries',
        'sort_order' => 107,
        'question' => 'Do you help with Punjabi or Hindi speaking clients?',
        'answer' => 'ਹਾਂ, ਅਸੀਂ ਪੰਜਾਬੀ ਅਤੇ ਹਿੰਦੀ ਵਿੱਚ ਸੇਵਾਵਾਂ ਪ੍ਰਦਾਨ ਕਰਦੇ ਹਾਂ। ਕੀ ਤੁਸੀਂ ਸਾਡੀ ਟੀਮ ਨਾਲ ਮੁਫ਼ਤ ਸਲਾਹ ਬੁੱਕ ਕਰਨਾ ਚਾਹੋਗੇ? (Yes, we offer services in Punjabi and Hindi — would you like to book a free consultation with our team?)',
        'match_signals' => 'punjabi|\bhindi\b|speak punjabi|speak hindi|language punjabi',
    ],
    [
        'category' => '3.1 General Enquiries',
        'sort_order' => 108,
        'question' => 'Can you help Bollywood or Punjabi artists get an Australian visa?',
        'answer' => 'We assist entertainment professionals including singers, artists, actors, and religious leaders — all handled with complete confidentiality. Would you like our team to assist you?',
        'match_signals' => 'bollywood|\bartist\b|\bcelebrity\b|punjabi singers|film actor|\bvisa for singer\b|\bvisa for dj\b|\bcelebrity visa\b',
    ],

    // ── 3.2 Skilled ────────────────────────────────────────────────────────────
    [
        'category' => '3.2 Skilled Migration',
        'sort_order' => 201,
        'question' => 'What is a subclass 189 visa?',
        'answer' => 'The 189 is a permanent skilled independent visa for people not sponsored by an employer or state government. Our consultants can explain this pathway in detail — would you like a free consultation?',
        'match_signals' => '\b189\b|subclass 189|skilled independent',
    ],
    [
        'category' => '3.2 Skilled Migration',
        'sort_order' => 202,
        'question' => 'What is the difference between 189, 190, and 491?',
        'answer' => 'We assist with all three — the 189 (independent), 190 (state-nominated), and 491 (regional) skilled migration visas. Our team can help identify which pathway may suit you — shall we book a consultation?',
        'match_signals' => '189.*190|190.*491|difference between 189|189 vs 190|189 and 190|skilled regional 491',
    ],
    [
        'category' => '3.2 Skilled Migration',
        'sort_order' => 203,
        'question' => 'How many points do I need for skilled migration?',
        'answer' => 'I\'m unable to assess points or eligibility here, but our consultants can review your full profile in detail. Would you like to book a free consultation?',
        'match_signals' => 'how many points|points test|points i need|points for 189|points for 190|points for 491|points score',
    ],
    [
        'category' => '3.2 Skilled Migration',
        'sort_order' => 204,
        'question' => 'Can a cook or chef apply for Australian PR?',
        'answer' => 'We assist clients across many skilled occupations. Our consultants can advise on occupation-specific pathways and skills assessments — would you like to connect with our team for a free consultation?',
        'match_signals' => '\bcook\b|\bchef\b|chef pr| cook pr',
    ],
    [
        'category' => '3.2 Skilled Migration',
        'sort_order' => 205,
        'question' => 'What is a skills assessment and which authority assesses my occupation?',
        'answer' => 'Skills assessments are conducted by authorities like ACS, Engineers Australia, VETASSESS, ANMAC, and TRA depending on your occupation. Our team can guide you through this process — would you like a consultation?',
        'match_signals' => 'skills assessment|vetassess|engineers australia|\bacs\b|\banmac\b|\btra\b|assessing authority',
    ],
    [
        'category' => '3.2 Skilled Migration',
        'sort_order' => 206,
        'question' => 'What is the Temporary Graduate 485 visa?',
        'answer' => 'The 485 allows recent Australian graduates to live and work in Australia after their studies. Our consultants can explain the pathways and conditions — shall we book a consultation?',
        'match_signals' => '\b485\b|temporary graduate|graduate visa|tr graduate',
    ],
    [
        'category' => '3.2 Skilled Migration',
        'sort_order' => 207,
        'question' => 'What is the Global Talent visa (GTI / NIV 858)?',
        'answer' => 'We assist with the National Innovation Visa (858) and Global Talent Independent program for highly skilled professionals. Our consultants can discuss if you may be suited — would you like to connect?',
        'match_signals' => '\b858\b|global talent|\bgti\b|national innovation visa|\bniv\b',
    ],
    [
        'category' => '3.2 Skilled Migration',
        'sort_order' => 208,
        'question' => 'What is the 887 Skilled Regional visa?',
        'answer' => 'The 887 is a permanent visa for skilled workers who have lived and worked in regional Australia. Our consultants can walk you through this pathway — would you like a free consultation?',
        'match_signals' => '\b887\b|skilled regional 887|887 visa',
    ],

    // ── 3.3 Partner & family ────────────────────────────────────────────────────
    [
        'category' => '3.3 Partner & Family Visas',
        'sort_order' => 301,
        'question' => 'My partner is an Australian citizen. How do I get a visa?',
        'answer' => 'We assist with partner visas including the 820/801 (onshore) and 309/100 (offshore) pathways. Our team can walk you through the full process — would you like a free consultation?',
        'match_signals' => 'partner.*australian citizen|australian partner|citizen partner| married to australian|spouse australian',
    ],
    [
        'category' => '3.3 Partner & Family Visas',
        'sort_order' => 302,
        'question' => 'What documents do I need for a partner visa?',
        'answer' => 'I\'m unable to provide personalised document lists here, but our consultants can give you a complete checklist based on your situation. Would you like to book a free consultation?',
        'match_signals' => 'partner visa.*document|documents for partner|checklist.*partner|evidence.*partner visa',
    ],
    [
        'category' => '3.3 Partner & Family Visas',
        'sort_order' => 303,
        'question' => 'I want to bring my parents to live in Australia',
        'answer' => 'We assist with several parent visa options including the 143, 103, 804, 864/884, and Sponsored Parent 870. Our consultants can help identify the right option for your family — would you like to speak with our team?',
        'match_signals' => 'bring my parents|parent visa|parents to australia|sponsor parent|mother father visa',
    ],
    [
        'category' => '3.3 Partner & Family Visas',
        'sort_order' => 304,
        'question' => 'I want to apply for a fiancé or prospective marriage visa',
        'answer' => 'We assist with the Prospective Marriage Visa (300). Our consultants can explain this process and what you need to prepare — would you like to book a free consultation?',
        'match_signals' => 'fianc|fiance|prospective marriage|visa 300|subclass 300',
    ],
    [
        'category' => '3.3 Partner & Family Visas',
        'sort_order' => 305,
        'question' => 'Can my child come to join me in Australia?',
        'answer' => 'We assist with child visas including subclasses 101, 802, and 102 (adoption). Our team can advise on the right option — shall we arrange a consultation?',
        'match_signals' => 'child.*visa|\bbring my child|join me in australia|dependent child\b|child migrate',
    ],
    [
        'category' => '3.3 Partner & Family Visas',
        'sort_order' => 306,
        'question' => 'What is the 461 NZ Citizen Family visa?',
        'answer' => 'The 461 allows non-NZ family members of NZ citizens to live and work in Australia. Our consultants can explain this pathway — would you like to connect with our team?',
        'match_signals' => '\b461\b|nz citizen family|new zealand partner|relationship with nz citizen',
    ],

    // ── 3.4 Student ─────────────────────────────────────────────────────────────
    [
        'category' => '3.4 Student Visas',
        'sort_order' => 401,
        'question' => 'I want to apply for a student visa to study in Australia',
        'answer' => 'We assist with Student Visa 500 applications, COE support, course changes, extensions, and post-study pathways. Would you like a free consultation with our team?',
        'match_signals' => 'student visa\b|visa 500|subclass 500|study.*australia\b|study in australia\b|study visa',
    ],
    [
        'category' => '3.4 Student Visas',
        'sort_order' => 402,
        'question' => 'My student visa is about to expire. What do I do?',
        'answer' => 'Student visa extensions need to be managed carefully and promptly. Please speak with our team as soon as possible — would you like to book an urgent consultation?',
        'match_signals' => 'student visa.*expire|student visa expires|visa expiring|extend student visa|about to expire',
    ],
    [
        'category' => '3.4 Student Visas',
        'sort_order' => 403,
        'question' => 'Can I work while on a student visa?',
        'answer' => 'I\'m unable to advise on visa conditions here, but our consultants can clarify your work rights in a consultation. Would you like to book one?',
        'match_signals' => 'work while studying|student visa.*work|work rights\b.*student|hours.*student visa',
    ],
    [
        'category' => '3.4 Student Visas',
        'sort_order' => 404,
        'question' => 'I want to change my course — will it affect my visa?',
        'answer' => 'Course changes can have implications for your student visa. Our team can guide you safely before you make any changes — would you like to book a consultation first?',
        'match_signals' => 'change course|change my course|course change|coe change| swapping course',
    ],
    [
        'category' => '3.4 Student Visas',
        'sort_order' => 405,
        'question' => 'What is a Professional Year program?',
        'answer' => 'Professional Year programs provide structured work experience for international graduates and can contribute points toward skilled migration. Our team can guide you — would you like a consultation?',
        'match_signals' => '\bprofessional year\b|\b py program\b|\bpy\b graduation',
    ],
    [
        'category' => '3.4 Student Visas',
        'sort_order' => 406,
        'question' => 'I deferred my studies. Will my visa be affected?',
        'answer' => 'Study deferral can affect your student visa status. Our consultants can review your specific situation — please book a consultation as soon as possible.',
        'match_signals' => '\bdeferred\b|\bdefer\b.*study|deferral\b',
    ],
    [
        'category' => '3.4 Student Visas',
        'sort_order' => 407,
        'question' => 'What is a Student Guardian visa 590?',
        'answer' => 'The 590 is for parents or relatives who come to Australia to support a student aged under 18. Our consultants can explain this process — would you like to connect with our team?',
        'match_signals' => '\b590\b|student guardian|guardian visa',
    ],

    // ── 3.5 Employer sponsored ─────────────────────────────────────────────────────
    [
        'category' => '3.5 Employer Sponsored Visas',
        'sort_order' => 501,
        'question' => 'My employer wants to sponsor me for a work visa',
        'answer' => 'We assist with employer-sponsored visas including the 482 (Skills in Demand) and 186 (ENS permanent). We work with both employers and employees — would you like a free consultation?',
        'match_signals' => 'employer sponsor|nominate me|\b482\b|\bens\b|work visa sponsorship|company sponsorship',
    ],
    [
        'category' => '3.5 Employer Sponsored Visas',
        'sort_order' => 502,
        'question' => 'What is a 482 Skills in Demand visa?',
        'answer' => 'The 482 is a temporary employer-sponsored work visa. Our consultants can explain the visa conditions, occupation lists, and sponsorship requirements — would you like to book a consultation?',
        'match_signals' => '\b482\b|skills in demand|skills-in-demand visa',
    ],
    [
        'category' => '3.5 Employer Sponsored Visas',
        'sort_order' => 503,
        'question' => 'What is an ENS 186 visa?',
        'answer' => 'The 186 is a permanent employer nomination visa. Our team can guide both you and your employer through the nomination and application process — shall we book a consultation?',
        'match_signals' => '\bens\b employer|186 visa|\b186\b|employer nomination scheme',
    ],
    [
        'category' => '3.5 Employer Sponsored Visas',
        'sort_order' => 504,
        'question' => 'Can a small business sponsor an overseas worker?',
        'answer' => 'Businesses must first become approved sponsors before nominating workers. We assist employers through the sponsorship process too — would you like to connect with our team?',
        'match_signals' => 'small business sponsor|sponsor overseas worker|\bstartup\b.*sponsor|family business sponsorship',
    ],
    [
        'category' => '3.5 Employer Sponsored Visas',
        'sort_order' => 505,
        'question' => 'What is the SESR 494 regional employer sponsored visa?',
        'answer' => 'The 494 is a temporary skilled employer-sponsored regional visa with a pathway to permanent residency. Our consultants can explain the full process — would you like a free consultation?',
        'match_signals' => '\b494\b|\bsesr\b|regional sponsorship|employer sponsored regional',
    ],

    // ── 3.6 Appeals (high priority matching) ───────────────────────────────────────
    [
        'category' => '3.6 Appeals & Refusals',
        'sort_order' => 601,
        'question' => 'My visa was refused. What can I do?',
        'answer' => 'I\'m sorry to hear that — this must be very stressful. This is best handled directly with one of our consultants. Please share your full name and phone number so our team can contact you personally as soon as possible.',
        'match_signals' => 'visa was refused|visa refusal|visa rejected|reject my visa|refused\b.*visa\b|decision refusal',
    ],
    [
        'category' => '3.6 Appeals & Refusals',
        'sort_order' => 608,
        'question' => 'My tourist visa was refused. Can I reapply?',
        'answer' => 'Visitor visa refusals need careful professional review before reapplying. Please share your name and phone number so our team can contact you and advise you properly.',
        'match_signals' => 'tourist.*refused|visitor.*refused|visitor visa refusal|visa refused visitor|visa refused tourists|refusal.*tourist',
    ],
    [
        'category' => '3.6 Appeals & Refusals',
        'sort_order' => 603,
        'question' => 'I received a NOICC letter. What does that mean?',
        'answer' => 'A NOICC is a very serious matter requiring urgent attention. Please share your name and phone number immediately — our consultants will contact you personally as a priority.',
        'match_signals' => 'noicc|notice of intention to consider cancellation|intention to consider cancellation',
    ],
    [
        'category' => '3.6 Appeals & Refusals',
        'sort_order' => 604,
        'question' => 'My visa was cancelled. Can I appeal?',
        'answer' => 'Visa cancellations are complex and time-sensitive matters. Please provide your name and phone number right away so our consultants can contact you directly and urgently.',
        'match_signals' => 'visa cancelled|visa cancellation|cancellation\b.*visa\b|cancelled\b.*visa\b',
    ],
    [
        'category' => '3.6 Appeals & Refusals',
        'sort_order' => 6045,
        'question' => 'What is an ART or AAT appeal?',
        'answer' => 'We assist with Administrative Review Tribunal (ART) appeals. These require direct consultation — please share your name and phone number so our team can reach you promptly.',
        'match_signals' => '\baat\b|administrative review tribunal|\btribunal appeal\b|\bmerits review tribunal\b',
    ],
    [
        'category' => '3.6 Appeals & Refusals',
        'sort_order' => 605,
        'question' => 'Can I apply for Ministerial Intervention?',
        'answer' => 'Ministerial Intervention is a complex and specific process. Our consultants can assess whether it may apply to your situation — please share your contact details so our team can reach you.',
        'match_signals' => 'ministerial intervention|minister intervention|request intervention',
    ],
    [
        'category' => '3.6 Appeals & Refusals',
        'sort_order' => 6055,
        'question' => 'I overstayed my visa. What are my options?',
        'answer' => 'This is a sensitive and urgent situation that needs immediate professional attention. Please share your name and phone number so our consultants can contact you as soon as possible.',
        'match_signals' => 'overstay\b|overstayed|visa expired and i am still\b|illegal stay\b|stay after visa expired',
    ],
    [
        'category' => '3.6 Appeals & Refusals',
        'sort_order' => 606,
        'question' => 'I received a s.501 character refusal. What do I do?',
        'answer' => 'A character-related refusal is extremely serious and time-sensitive. Please share your full name and phone number immediately — our consultants will prioritise your case.',
        'match_signals' => '501 character|\bs501\b|\bcharacter refusal\b|\bcharacter cancellation\b|section 501',
    ],

    // ── 3.7 Citizenship ────────────────────────────────────────────────────────────
    [
        'category' => '3.7 Citizenship',
        'sort_order' => 701,
        'question' => 'How do I apply for Australian citizenship?',
        'answer' => 'We assist with citizenship by conferral, by descent, and by birth. Our consultants can guide you to the correct pathway — would you like a free consultation?',
        'match_signals' => '\bcitizenship\b|\baustralian citizenship\b|apply citizenship|citizenship conferral|citi.*ship australia',
    ],
    [
        'category' => '3.7 Citizenship',
        'sort_order' => 702,
        'question' => 'How long do I need to live in Australia before applying for citizenship?',
        'answer' => 'Residency requirements depend on your visa history and individual circumstances — our consultants can assess this properly. Would you like to book a free consultation?',
        'match_signals' => 'citizenship.*residency|how long\b.*citizenship|residency requirement|citi.*requirements',
    ],
    [
        'category' => '3.7 Citizenship',
        'sort_order' => 703,
        'question' => 'Can I hold dual citizenship with Australia?',
        'answer' => 'Dual citizenship rules depend on both Australia and your home country. Our consultants can discuss your specific situation — would you like to connect with our team?',
        'match_signals' => 'dual citizen|dual nationality|dual citizenship|citi.*countries',
    ],
    [
        'category' => '3.7 Citizenship',
        'sort_order' => 704,
        'question' => 'I need help preparing for my citizenship test',
        'answer' => 'We assist with citizenship test preparation. Our team can help you get ready — would you like to book a consultation?',
        'match_signals' => 'citizenship test|citi.*test\b|aust.*citizens.*test|citi exam',
    ],
    [
        'category' => '3.7 Citizenship',
        'sort_order' => 705,
        'question' => 'I was born in Australia but I\'m not sure about my citizenship status',
        'answer' => 'Citizenship by birth is a nuanced area. Our consultants can clarify your status properly — would you like to speak with our team?',
        'match_signals' => '\bborn.*australia|citi.*birth|birth citizenship',
    ],

    // ── 3.8 Visitor ─────────────────────────────────────────────────────────────────
    [
        'category' => '3.8 Visitor Visas',
        'sort_order' => 801,
        'question' => 'I want to visit my family in Australia',
        'answer' => 'We assist with visitor visas including the Subclass 600 and Sponsored Family Visitor options. Our consultants can guide you through the application process — would you like a free consultation?',
        'match_signals' => 'visit family|family visitor|visa to visit australia|Subclass 600|visit my parents australia|tourist visa family',
    ],
    [
        'category' => '3.8 Visitor Visas',
        'sort_order' => 802,
        'question' => 'Can I extend my visitor visa while in Australia?',
        'answer' => 'Visitor extensions may be possible depending on your circumstances. Our consultants can review your situation — would you like to book a consultation?',
        'match_signals' => 'extend\b.*visitor\b|visitor.*extend|Subclass 600.*extend|\bvisa extension\b',
    ],
    [
        'category' => '3.8 Visitor Visas',
        'sort_order' => 803,
        'question' => 'What is a Working Holiday visa (417 / 462)?',
        'answer' => 'We assist with Work and Holiday visas (417 and 462). Our consultants can explain eligibility and the application process — would you like a free consultation?',
        'match_signals' => '\b417\b|\b462\b|working holiday|work and holiday|whv\b|backpacker',
    ],
    [
        'category' => '3.8 Visitor Visas',
        'sort_order' => 804,
        'question' => 'I need a medical treatment visa to come to Australia',
        'answer' => 'We assist with the Medical Treatment Visa (602). Our consultants can guide you through the process and documentation — would you like to connect with our team?',
        'match_signals' => 'medical treatment visa|\b602\b|visa for surgery|visa for treatment australia',
    ],

    // ── Section 2 generic escalation ───────────────────────────────────────────────
    [
        'category' => '2.3 Escalation fallback',
        'sort_order' => 970,
        'question' => 'Escalate to consultant (protection / federal court)',
        'answer' => 'This is best discussed directly with one of our consultants. Please share your full name and phone number and our team will contact you personally.',
        'match_signals' => 'protection visa|refugee claim|humanitarian visa|ministerial waiver|panic attack|suicidal|suicidal thoughts|suicidal ideation|\bfederal court\b|federal court appeal',
    ],
    [
        'category' => '7 Office',
        'sort_order' => 971,
        'question' => 'Legal advice pattern',
        'answer' => 'Our registered MARA consultants can assist you properly during a consultation — I\'m not able to provide legal advice here. Would you like to book a free consultation?',
        'match_signals' => '\blegal advice\b|migration act.*advice|tell me legally|breaking the immigration law\b',
    ],

    // ── Section 5 — Services Reference: visa types not covered above ─────────────

    // Bridging Visas
    [
        'category' => '5 Services Reference',
        'sort_order' => 1101,
        'question' => 'What is a Bridging Visa A (010)?',
        'answer' => 'We assist with Bridging Visa A (010) matters, which allow you to remain lawfully in Australia while a substantive visa is being processed. Our consultants can guide you — would you like a free consultation?',
        'match_signals' => '\bbridging visa\b|\bbva\b|\b010\b|bridging visa a\b',
    ],
    [
        'category' => '5 Services Reference',
        'sort_order' => 1102,
        'question' => 'What is a Bridging Visa B (020) — can I travel while waiting for my visa?',
        'answer' => 'The Bridging Visa B (020) allows you to travel outside Australia while your substantive visa application is pending. Our consultants can advise on eligibility and conditions — would you like to book a consultation?',
        'match_signals' => '\bbvb\b|\b020\b|bridging visa b\b|travel.*bridging|bridging.*travel',
    ],
    [
        'category' => '5 Services Reference',
        'sort_order' => 1103,
        'question' => 'What is a Bridging Visa C (030)?',
        'answer' => 'The Bridging Visa C (030) allows certain applicants to stay in Australia while their visa application is being processed. Our consultants can clarify your situation — would you like a free consultation?',
        'match_signals' => '\bbvc\b|\b030\b|bridging visa c\b',
    ],
    [
        'category' => '5 Services Reference',
        'sort_order' => 1104,
        'question' => 'What is a Bridging Visa E (050 / 051)?',
        'answer' => 'The Bridging Visa E (050/051) is for people who are unlawfully present in Australia or whose visa has ceased. This is an urgent matter — please share your name and phone number so our consultants can contact you right away.',
        'match_signals' => '\bbve\b|\b050\b|\b051\b|bridging visa e\b|unlawful.*bridging',
    ],

    // Resident Return Visa
    [
        'category' => '5 Services Reference',
        'sort_order' => 1105,
        'question' => 'I need a Resident Return Visa (155 / 157) — my PR travel facility has expired',
        'answer' => 'We assist with Resident Return Visas (155/157) for permanent residents who need to return to Australia. Our consultants can review your situation — would you like a free consultation?',
        'match_signals' => '\b155\b|\b157\b|resident return|\brrv\b|permanent resident.*travel|pr.*travel.*expired',
    ],

    // 191 Permanent Residence Skilled Regional
    [
        'category' => '5 Services Reference',
        'sort_order' => 1106,
        'question' => 'What is the 191 Permanent Residence (Skilled Regional) visa?',
        'answer' => 'The 191 is a permanent visa for holders of the 491 or 494 regional visas who have met the required regional living and working conditions. Our consultants can walk you through this pathway — would you like a free consultation?',
        'match_signals' => '\b191\b|permanent.*skilled regional|191 permanent|491.*permanent|494.*permanent',
    ],

    // Temporary work visas
    [
        'category' => '5 Services Reference',
        'sort_order' => 1107,
        'question' => 'What is a 407 Training Visa?',
        'answer' => 'The 407 is a temporary visa for people who want to undertake occupational training or professional development in Australia. Our consultants can explain eligibility and the process — would you like to book a consultation?',
        'match_signals' => '\b407\b|training visa|occupational training visa',
    ],
    [
        'category' => '5 Services Reference',
        'sort_order' => 1108,
        'question' => 'What is a 408 Temporary Activity visa?',
        'answer' => 'The 408 covers a range of temporary activities including entertainment, religious work, and special programs in Australia. Our consultants can advise on the right category for your situation — would you like to connect with our team?',
        'match_signals' => '\b408\b|temporary activity visa',
    ],
    [
        'category' => '5 Services Reference',
        'sort_order' => 1109,
        'question' => 'What is a 400 Short Stay Specialist visa?',
        'answer' => 'The 400 is a short-term visa for people with highly specialised skills required in Australia for a specific purpose. Our consultants can explain the eligibility requirements — would you like a free consultation?',
        'match_signals' => '\b400\b|short stay specialist|specialist short stay',
    ],
    [
        'category' => '5 Services Reference',
        'sort_order' => 1110,
        'question' => 'What is Global Talent Employer Sponsored (GTES)?',
        'answer' => 'The GTES program allows approved employers to sponsor highly skilled global talent outside the standard occupation lists. Our consultants can assess whether this pathway suits your situation — would you like to connect with our team?',
        'match_signals' => '\bgtes\b|global talent employer sponsored|employer.*global talent',
    ],

    // Family — remaining relative & carer
    [
        'category' => '5 Services Reference',
        'sort_order' => 1111,
        'question' => 'What is a Remaining Relative visa (115 / 835)?',
        'answer' => 'We assist with Remaining Relative visas (115 offshore / 835 onshore) for those whose only close family members are Australian citizens or permanent residents. Our consultants can guide you — would you like a free consultation?',
        'match_signals' => '\b115\b|\b835\b|remaining relative|only relative.*australia',
    ],
    [
        'category' => '5 Services Reference',
        'sort_order' => 1112,
        'question' => 'What is a Carer visa (116 / 836)?',
        'answer' => 'We assist with Carer visas (116 offshore / 836 onshore) for people who want to care for an Australian citizen or permanent resident with a medical condition. Our consultants can explain the process — would you like to book a consultation?',
        'match_signals' => '\b116\b|\b836\b|\bcarer visa\b|visa.*carer|carer.*visa',
    ],

    // eVisitor & Pacific
    [
        'category' => '5 Services Reference',
        'sort_order' => 1113,
        'question' => 'What is a 651 eVisitor visa?',
        'answer' => 'The 651 eVisitor allows eligible passport holders from certain European and other countries to visit Australia for tourism or business. Our consultants can confirm your eligibility and assist — would you like to connect with our team?',
        'match_signals' => '\b651\b|\bevisitor\b|e-visitor visa',
    ],
    [
        'category' => '5 Services Reference',
        'sort_order' => 1114,
        'question' => 'What is the 192 Pacific Engagement visa?',
        'answer' => 'The 192 Pacific Engagement visa is for eligible nationals from Pacific island countries and Timor-Leste to live and work in Australia permanently. Our consultants can advise on eligibility — would you like a free consultation?',
        'match_signals' => '\b192\b|pacific engagement|pacific.*visa',
    ],

    // ── Section 6 — Standalone Quick-Reference Templates ───────────────────────

    [
        'category' => '6 Quick templates',
        'sort_order' => 4,
        'question' => 'General PR or skilled migration enquiry',
        'answer' => 'We assist with skilled migration and PR pathways including the 189, 190, and 491 visas. Please speak with our team for detailed assistance — would you like to book a free consultation?',
        'match_signals' => '^pr$|^permanent residency$|i want pr\b|i want permanent residency|skilled migration.*pr\b|pr.*skilled migration|\bget my pr\b',
    ],
    [
        'category' => '6 Quick templates',
        'sort_order' => 5,
        'question' => 'Request a callback',
        'answer' => 'Please share your full name, phone number, visa type, and the best time for a callback — our team will reach out to you personally.',
        'match_signals' => 'call me back|callback|call back|reach me personally|contact me back|get back to me|my number is\b|please call me',
    ],
    [
        'category' => '6 Quick templates',
        'sort_order' => 6,
        'question' => 'How do I book a consultation or appointment?',
        'answer' => 'You can book a free 10-minute consultation with our team at www.bansalimmigration.com.au/book-an-appointment. Would you like us to arrange a callback instead?',
        'match_signals' => 'how.*book.*consult|how.*book.*appointment|book.*online|schedule.*appointment|arrange.*consult|make.*appointment',
    ],
];
