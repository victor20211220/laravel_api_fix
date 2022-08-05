"use strict";
/**
* Preset variables
*/

let key; // publisherKey
const userLandingUrlAlternative = 'http://www.notfound.com'; // LandingUrl alternative if nor available in browser
const apiUrl = 'https://fmgroupdist.com/hlola'; // base url API
const adsUrl = 'https://filthymediagroup.com/?utm_source=bdsmtest'; // url for <a href=”https://example.com” target = “_blank”>Advertise With Us</a>

let device;

// userAgent header for comparing to below object to get OS and browser
const header = [navigator.platform, navigator.userAgent, navigator.appVersion, navigator.vendor, window.opera];

const dataOs = [ // pre defined existing OS
    {name: 'Windows Phone', value: 'Windows Phone', version: 'OS', type: 'mobile'},
    {name: 'Windows', value: 'Win', version: 'NT', type: 'desktop'},
    {name: 'iPhone', value: 'iPhone', version: 'OS', type: 'mobile'},
    {name: 'iPad', value: 'iPad', version: 'OS', type: 'mobile'},
    {name: 'Kindle', value: 'Silk', version: 'Silk', type: 'mobile'},
    {name: 'Android', value: 'Android', version: 'Android', type: 'mobile'},
    {name: 'PlayBook', value: 'PlayBook', version: 'OS', type: 'mobile'},
    {name: 'BlackBerry', value: 'BlackBerry', version: '/', type: 'mobile'},
    {name: 'Macintosh', value: 'Mac', version: 'OS X', type: 'desktop'},
    {name: 'Linux', value: 'Linux', version: 'rv', type: 'desktop'},
    {name: 'Palm', value: 'Palm', version: 'PalmOS', type: 'mobile'}
];

const dataBrowser = [ // pre defined existing web browsers
    {name: 'Chrome', value: 'Chrome', version: 'Chrome'},
    {name: 'Firefox', value: 'Firefox', version: 'Firefox'},
    {name: 'Safari', value: 'Safari', version: 'Version'},
    {name: 'Internet Explorer', value: 'MSIE', version: 'MSIE'},
    {name: 'Opera', value: 'Opera', version: 'Opera'},
    {name: 'BlackBerry', value: 'CLDC', version: 'CLDC'},
    {name: 'Mozilla', value: 'Mozilla', version: 'Mozilla'}
];

// variables for click detection iframe
let mouseover = false,
	tab = false,
	failCount = 0;


/**
* object containing all logic
*/

const API = {
	async getIp(){ // api request to get IP and Landcode from user
		try{
			fetch('https://ipwhois.pro/?key=DgYF9J7sf7xDyQwr').then(res => res.json()).then((d)=>{
				var userLang = navigator.language || navigator.userLanguage; 
				return {
					sessionIp: (d.ip ? d.ip : '000.000.000'), 
					sessionCountry: (d.country_code ? d.country_code : userLang),
					userLandingUrl: window.location.href || userLandingUrlAlternative,
					  userInboundUrl: window.referrer || 'NONE',
					status: 'success'
				};
			});
		} catch(err){
			return {status: 'failed'};
		};
	},

	async getUsetAgent(){ // check what browser and os
		try{
			const agent = header.join(' '); // create clean string from userAgent header
			let matchesOs;
			let type;
			for(let i = 0; i < dataOs.length; i++){ // loop and search for OS
		        const regex = new RegExp(dataOs[i].value, 'i');
		        const matchOs = regex.test(agent);
		        if(matchOs){
		            matchesOs = dataOs[i].name;
					type = dataOs[i].type;
		            break;
		        };
		    };

		    let matchesBrowser;
		    for(let i = 0; i < dataBrowser.length; i++){ // loop and search for Browser
		        const regex = new RegExp(dataBrowser[i].value, 'i');
		        const matchBrowser = regex.test(agent);
		        if(matchBrowser){
					device = dataBrowser[i].type;
		            matchesBrowser = dataBrowser[i].name;
		            break;
		        };
		    };
			return {sessionBrowser: matchesBrowser, sessionOs: matchesOs, type: type, status: 'success'};
		} catch(err){
			return {status: 'failed'};
		};
	},

	async getSession(d, device){ // get new session from api
		try{
			const user = {
				"publisherKey": key,
				"sessionIp": d.sessionIp,
				"sessionOs": device.sessionOs,
				"sessionBrowser": device.sessionBrowser,
				"sessionCountry": d.sessionCountry,
				"userInboundUrl": d.userInboundUrl,
				"userLandingUrl": d.userLandingUrl
			};
	
			return fetch(apiUrl+"/global/ubambo", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(user)
			}).then(res=>res.json()).then(res=>{
				return res;
			});
		} catch(err){
			failCount++;
			return {status: 'failed'};
		};
	},

	async checkSession(d){ // check session
		try{
			const user = {
				"publisherKey": key,
				"sessionKey": d.sessionKey
			};
	
			return fetch(apiUrl+"/global/ubambo/check", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(user)
			}).then(res=>res.json()).then(res=>{
				return res;
			});
		} catch(err){
			failCount++;
			return {status: 'failed'};
		};
	},

	async getLocalSession(){ // check if local session cookie exists
		try{
            const session = document.cookie.split(';'); // split cookie into array
            let part = [],
                sessionObj = {};
            const date = new Date().getTime(); // get current dat for comparing

            session.forEach(item=>{ // search for cookie name "sessionKey="
                if(item.includes('sessionKey=')){
                    part = item.split('&'); // cookie is save with all parameters in value, sessionKey=7adQbt5WL&creationDate=2022-07-14 14:27:18&userKey=BjYp1xmZW
                };
            });

            if(Object.keys(part).length < 1){ // no session
                sessionObj['status'] = 'failed';
                return sessionObj;
            } else { // have session
                const sessionDate = new Date(sessionObj.creationDate).getTime();
                if(sessionDate > (date - 90 * 24 * 60 * 60 * 1000) || sessionObj.sessionIp == '000.000.000'){ // check timestamp no older then 90 days
                    sessionObj['status'] = 'failed';
                    return sessionObj;
                } else { // all check pass, no need to request new session
                    sessionObj['status'] = 'success';
                    part.forEach(item=>{
                        const objItem = item.split('=');
                        const name = objItem[0].replace(' ', '');
                        const value = objItem[1];
                        sessionObj[name] = value;
                    });
                    return sessionObj;
                };
            };
        } catch(err){
            return {status: 'failed'};
        };
	},

	async setLocalSession(data, network){ // save or update session to local cookie
		try{
			const {sessionKey, dateCreated, userKey} = data;
			const {sessionIp, sessionCountry} = network;
			document.cookie = `sessionKey=${sessionKey}&creationDate=${dateCreated}&userKey=${userKey}&ip=${sessionIp}&country=${sessionCountry}$SameSite=None`;
		} catch(err){
			return;
		};
	},

	async getScores(){ // get list of "getresult" items from DOM and return first 8
		try{
			const resultCount = document.querySelectorAll('.result_row'); // create array from all ads div in the DOM
			let scores = [];
			if(resultCount.length > 0){ // checking div array, biger then 0 meaning we have "getresult"
   				resultCount.forEach((item, i)=>{
		   			if(i < resultCount.length || i < 7){
		   				const parent = item.children;
		   				scores.push(parent.item(3).innerText); // get names for each of the first 8 getresult
		   			};
		   		});
				// ads are set, now check if getResult true/false
		   		return {scores: scores, status: 'success'};
		   	} else {
		   		return {scores: '', status: 'failed'};
		   	};
		} catch(err){
			return {scores: '', status: 'failed'};
		};
	},

	async getGlobal(){ // request for global ads
		try{
			const obj = {
				"publisherKey": key
			};
	
			return fetch(apiUrl+"/global/funa", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(obj)
			}).then(res=>res.json()).then(res=>{
				return res;
			});
		} catch(err){
			failCount++;
			return {status: 'failed'};
		};
	},

	async getContextual(d){ // request for contextual ads
		try{
			const obj = {
				"publisherKey": key,
				"keywordText": d.join(',')
			};
	
			return fetch(apiUrl+"/contextual/funa", {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(obj)
			}).then(res=>res.json()).then(res=>{
				return res;
			});
		} catch(err){
			failCount++;
			return {status: 'failed'};
		};
	},

	async prepareImg(selected, raw, session, type, last){ // create image objects
		try{
			const position = (last ? last : selected.advertPosition);
			const img = {
				position: position,
				imgUrl: selected.advertImagePath,
				href: selected.advertUrl
			};
	
			const load = { // object for impression register
				"publisherKey": key,
				"sessionKey": session.sessionKey,
				"campaignKey": "NONE",
				"advertKey": selected.advertKey,
				"impressionsUrl": window.location.href,
				"impressionsBackfill": "FALSE",
				"backfillKey": ""
			};
	
			this.handleImages(img, load, type);
		} catch(err){
			return;
		};
	},

    async handleImages(image, load, t){ // all logic for placing images
		try{
			const {position, imgUrl} = image;
			const imgLocation = document.querySelector(`#fm-${position}`);
			if(!imgLocation) return;
			const img = `<img src="${imgUrl}" style="cursor: pointer; background-size: auto; background-repeat: no-repeat; object-fit: none; display: block; margin: auto"/>`; // image element for the ad
			const link = document.createElement('a');
			link.innerText = "Advertise With Us";
			link.title = "Advertise With Us";
			link.href = adsUrl;
			link.setAttribute('target', 'blank');
			link.style.fontSize = '11px';
			link.style.display = 'block';
			link.style.textAlign = 'center';
			const addOrNot = (position == '5' || position == '6c' ? link : ''); // check if we need to add extra link
			imgLocation.after(addOrNot);
			imgLocation.innerHTML = img;
			imgLocation.style.display = 'block'; // set display: block for each ad we activate
			const imageElement = imgLocation.children[0]; // get the image as node after placing it in the DOM
	
			imageElement.addEventListener("load", (e)=>{ // create image loaded eventListener per ad
				this.registerImpression(load, img, t, imageElement, image); // register impression
			});
		} catch(err){
			return;
		};
    },

    async registerImpression(d, e, t, i, r){ // if image loaded start the impression registration function
		try{
			const endpoint = (t.type == 'contextual' ? '/contextual/mamatheka' : '/global/mamatheka'); // check what endpoint to use global/contextual
			return fetch(apiUrl+endpoint, {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(d)
			}).then(res=>res.json()).then(res=>{
				d['impressionKey'] = res.impressionKey; // add impression key to object for register click
				i.addEventListener("click", ()=>{ // create click eventListener per ad
					this.registerClick(d, t); // register click
					window.open(r.href+'?utm_source=bdsmtest', '_blank'); // redirect on click
				});
			});
		} catch(err){
			failCount++;
			return;
		};
    },

    async registerClick(d, t){ // register click on ad
		try{
			const endpoint = (t.type == 'contextual' ? '/contextual/isipho' : '/global/isipho'); // check what endpoint to use global/contextual
			return fetch(apiUrl+endpoint, {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(d)
			});
		} catch(err){
			failCount++;
			return;
		};
    },

    async generateRandom(min, max){ // get random number within range
		try{
			let difference = max - min;
			let rand = Math.random();
			rand = Math.floor(rand * difference);
			rand = rand + min;
			return rand;
		} catch(err){
			return;
		};
	},

	async setDisplay(device){ // once page loaded get all ad divs and set display:none
		try{
			const ads = document.querySelectorAll('[id^="fm-"]');
			ads.forEach(item=>{
				if(item.id == 'fm-2' && device.type == 'mobile'){ // add html to fm-2 and fm-4
					setLink();
				} else if(item.id == 'fm-4' && device.type == 'desktop'){
					setLink();
				};

				function setLink(){
					const link = document.createElement('a');
					link.innerText = "Advertise With Us";
					link.title = "Advertise With Us";
					link.href = adsUrl;
					link.setAttribute('target', 'blank');
					link.style.fontSize = '11px';
					link.style.display = 'block';
					link.style.textAlign = 'center';
					item.after(link);
				};
			});
		} catch(err){
			return;
		};
	},

	async start(device){ // Start the process after DOM loading ready and check if test mode
		try{
			let localSession = await this.getLocalSession();
			let ads;

			// check if session exists if not get from api
			if(localSession.status.toLowerCase() == 'failed' || !localSession || localSession.sessionIp == '000.000.000'){
				let network = await API.getIp();
				localSession = await this.getSession(network, device);
				this.setLocalSession(localSession, network);
			};

			// checking "getresult"
			const result = await this.getScores();
			if(result.status.toLowerCase() != 'failed'){ // have getresults, getContextual
				ads = await this.getContextual(result.scores);
				if(ads.status.toLowerCase() == 'failed'){
					ads = await this.getGlobal(result.scores);
					if(ads.status.toLowerCase() == 'failed') return;
					this.global(ads, localSession, device);
				} else {
					if(device.type == 'mobile'){
						const test0 = ads.contextualDisplay[0];
						const test1 = ads.contextualDisplay[1];
						if(test0 == "no available advert" && test1 == "no available advert"){
							ads = await this.getGlobal(result.scores);
							if(ads.status.toLowerCase() == 'failed') return;
							this.global(ads, localSession, device);
						} else {
							this.contextual(ads, localSession, device);
						};
					} else if(device.type == 'desktop'){
						const test2 = ads.contextualDisplay[2];
						const test3 = ads.contextualDisplay[3];
						const test4 = ads.contextualDisplay[4];
						if(test2 == "no available advert" && test3 == "no available advert" && test4 == "no available advert"){
							ads = await this.getGlobal(result.scores);
							if(ads.status.toLowerCase() == 'failed') return;
							this.global(ads, localSession, device);
						} else {
							this.contextual(ads, localSession, device);
						};
					};
				};
			} else { // no getresults, getGlobal
				ads = await this.getGlobal(result.scores);
				if(ads.status.toLowerCase() == 'failed') return;
				this.global(ads, localSession, device);
			};
		} catch(err){
			return;
		};
	},

	async registerIframeClick(i){
		try{
			const load = { // object for impression register
				"publisherKey": key,
				"sessionKey": i.getAttribute('session-key'),
				"campaignKey": "NONE",
				"advertKey": i.getAttribute('fmg-display'),
				"impressionsUrl": window.location.href,
				"impressionsBackfill": "FALSE",
				"backfillKey": "",
				"impressionKey": i.getAttribute('impression-key')
			};
	
			return fetch(apiUrl+'/global/isipho', {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(load)
			});
		} catch(err){
			failCount++;
			return;
		};
	},

	async registerIframeImpression(i){
		try{
			const load = { // object for impression register
				"publisherKey": key,
				"sessionKey": i.getAttribute('session-key'),
				"campaignKey": "NONE",
				"advertKey": i.getAttribute('fmg-display'),
				"impressionsUrl": window.location.href,
				"impressionsBackfill": "FALSE",
				"backfillKey": ""
			};
	
			return fetch(apiUrl+'/global/mamatheka', {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(load)
			}).then(res=>res.json()).then(res=>{
				i.setAttribute('impression-key', res.impressionKey);
			});
		} catch(err){
			failCount++;
			return;
		};
	},

	async scriptTag(i, d, s, t, l){
		try{
			const {advertImagePath, advertKey, advertPosition} = i;
			const position = (l ? l : advertPosition);
			const clean = advertImagePath.replace('async', '').replace('defer', ''); // remove defer, async tag
			const obj = clean.split(' '); // split into seperate items for each attribute
			const elem = document.createElement('script'); // create empty script tag

			obj.forEach(item=>{
				if(item.length != 0 && !item.includes('<')){ // make sure not to include empty items or the '<script' open or close tag
					const name = item.split('=')[0];
					const value = item.split('=')[1].replaceAll('"', '');
					elem.setAttribute(name, value);
				};
			});

			const imgLocation = document.querySelector(`#fm-${position}`);
			if(!imgLocation) return;
			imgLocation.appendChild(elem);
			imgLocation.style.display = 'block';
			imgLocation.style.textAlign = 'center';
			imgLocation.setAttribute('fmg-display', advertKey); // need to add reference to parent element for later click registration
			imgLocation.setAttribute('session-key', s.sessionKey);
		} catch(err){
			return;
		};
	},

	async global(d, s, t){ // handle global ads
		try{
			this.clearAll();
			const {globalDisplay} = d;
			for(let i = 1; i < 5; i++){ // loop trough images from result
				if(globalDisplay[i] != 'no available advert'){
					if(globalDisplay[i].advertUrl == 'MEDIATION'){
						if(t.type == 'mobile' && i < 3){
							this.scriptTag(globalDisplay[i], d, s, {type: 'global'});
						} else if(t.type == 'desktop' && i > 2){
							this.scriptTag(globalDisplay[i], d, s, {type: 'global'});
						};
					} else {
						const item = globalDisplay[i];
						if(t.type == 'mobile' && i < 3){
							this.prepareImg(item, d, s, {type: 'global'});
						} else if(t.type == 'desktop' && i > 2){
							this.prepareImg(item, d, s, {type: 'global'});
						};
					};
				};
			};

			if(t.type == 'mobile') return; // if mobile, no need to show ad 5 or 6

			// checking last 2 ads items in global
			if(globalDisplay[5] != 'no available advert' && globalDisplay[6] != 'no available advert'){ // both available
				const select = await this.generateRandom(5, 7); // select random 5 or 6
				if(select == 6){
					for(let i = 0; i < 3 ; i++){
						const item = globalDisplay[6][i];
						const pikName = (i == 0 ? '6a' : (i == 1 ? '6b' : '6c'));
						if(item.advertUrl == 'MEDIATION'){
							this.scriptTag(item, d, s, {type: 'global'}, pikName);
						} else {
							this.prepareImg(item, d, s, {type: 'global'}, pikName);
						};
					};
				} else {
					const item = globalDisplay[5];
					if(item.advertUrl == 'MEDIATION'){
						this.scriptTag(item, d, s, {type: 'global'});
					} else {
						this.prepareImg(item, d, s, {type: 'global'});
					};
				};
			} else if(globalDisplay[5] != 'no available advert'){ // only 5 is available
				const item = globalDisplay[5];
				if(item.advertUrl == 'MEDIATION'){
					this.scriptTag(item, d, s, {type: 'global'});
				} else {
					this.prepareImg(item, d, s, {type: 'global'});
				};
			} else if(globalDisplay[6] != 'no available advert') { // only 6 available
				for(let i = 0; i < 3 ; i++){
					const item = globalDisplay[6][i];
					const pikName = (i == 0 ? '6a' : (i == 1 ? '6b' : '6c'));
					if(item.advertUrl == 'MEDIATION'){
						this.scriptTag(item, d, s, {type: 'global'}, pikName);
					} else {
						this.prepareImg(item, d, s, {type: 'global'}, pikName);
					};
				};
			};
		} catch(err){
			return;
		};
	},

	async contextual(d, s, t){ // handle contextual ads
		try{
			this.clearAll();
			const {contextualDisplay} = d;
			const length = Object.keys(contextualDisplay).length;

			for(let i = 1; i < length; i++){ // loop trough images from result
				if(contextualDisplay[i] != 'no available advert'){

					const img = { // object for creating ad image element
						position: contextualDisplay[i].advertPosition,
						imgUrl: contextualDisplay[i].advertImagePath,
						href: contextualDisplay[i].advertUrl
					};

					const load = { // object for impression register
						"publisherKey": key,
						"sessionKey": s.sessionKey,
						"userKey": s.userKey,
						"keywordKey": d.keywordKey,
						"advertKey": contextualDisplay[i].advertKey
					};

					if(t.type == 'mobile' && i < 3){
						this.handleImages(img, load, {type: 'contextual'});
					} else if(t.type == 'desktop' && i > 2){
						this.handleImages(img, load, {type: 'contextual'});
					};
				};
			};
		} catch(err){
			return;
		};
	},

	async clearAll(){
		try{
			const ads = document.querySelectorAll('[id^="fm-"]'); // clear all content of ads before loop
			ads.forEach(item=>{
				item.removeAttribute('fmg-display');
				item.removeAttribute('session-key');
				item.removeAttribute('impression-key');
				item.style.display = 'none';
				item.style.margin = 'auto';
				item.innerHTML = '';
			});
		} catch(err){
			return;
		};
	},

	async checkClick(elem){
		try{
			if(elem && elem.tagName == 'IFRAME' && !tab && mouseover){ // check if activeElement is iframe, not tab key action and mouseover iframe
				const data = elem.parentNode.getAttribute('fmg-display');
				if(data){ // making sure that the clicked iframe has 'fmg-display' tag
					API.registerIframeClick(elem.parentNode);
				};
			};
			tab = false;
		} catch(err){
			return;
		};
	},

	async frameListener(frame){ // eventlistener per iframe to know when hover
		try{
			frame.addEventListener('mouseover', ()=>{
				mouseover = true;
			});	

			frame.addEventListener('mouseout', ()=>{
				setTimeout(()=>{
					mouseover = false;
				}, 1000);
			});
		} catch(err){
			return;
		};
	},

	async eventAndChange(){
		try{
			//listening for any change in the DOM that might be iframe or getresult
			MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
			const observer = new MutationObserver((mutations, observer)=>{
				mutations.forEach(item=>{
					if(item.nextSibling && item.nextSibling.localName == 'iframe'){
						const data = item.nextSibling.parentNode.getAttribute('fmg-display');
						if(data){ // only if tag 'fmg-display'
							API.registerIframeImpression(item.nextSibling.parentNode); // register impression after iframe loaded
							API.frameListener(item.nextSibling); // start click event listener
						};
					};
				});

				const getResult = document.querySelector('#maintableform');
				if(getResult){
					StartAds();
					observer.disconnect();
				};
			});

			observer.observe(document, {
				childList: true,
				subtree: true,
				attributes: true
			});

			window.addEventListener('blur', ()=>{
				setTimeout(()=>{ // firefox take a bit longet to change the activeElement, we wait before checking if iframe is the active element
					API.checkClick(document.activeElement);
				}, 500);
			});

			document.addEventListener('mousemove', ()=>{ // this sets back the focus to document if the activeElement is iframe
				var elem = document.activeElement;
				if(elem && elem.tagName == 'IFRAME'){
					window.focus();
				};
			});

			document.addEventListener('touchmove', ()=>{ // this sets back the focus to document if the activeElement is iframe for mobile
				var elem = document.activeElement;
				if(elem && elem.tagName == 'IFRAME'){
					window.focus();
				};
			});

			document.addEventListener('keydown', (e)=>{ // ignore blur by Tab key
				if(e.key == 'Tab'){
					tab = true;
				};
			}, false);


			// listen to click event from specific button
			const button = document.querySelector('#bigbuttonNext');

			if(button){
				button.addEventListener('click', ()=>{
					API.startup();
				});
			};
		} catch(err){
			return;
		};
	},

	async loop(){ // interval to restart the ads function from start every 60 seconds - 60000 miliseconds
		setInterval(()=>{
			const exists = document.querySelectorAll('[title="Advertise With Us"]'); // find all existing ads urls and remove before start over
			if(exists){
				exists.forEach(item=>{
					item.remove();
				});
			};
			if(failCount < 5){ // failCount filter
				API.startup();
			};
		}, 60000); // set miliseconds
	},

	async startup(){
		try{
			key = document.querySelector('#fmg-display').getAttribute('data-publisherKey');
			const device = await API.getUsetAgent();
			API.setDisplay(device); // first set all ad div to display none and add extra html
			API.start(device); // start without check mode
			API.eventAndChange();
			API.loop();
		} catch(err){
			return;
		};
	}
};

API.startup();