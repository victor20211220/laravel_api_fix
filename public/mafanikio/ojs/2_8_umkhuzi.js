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

/**
* object containing all logic
*/

const API = {
	async getIp(){ // api request to get IP and Landcode from user
		console.time('IpRequest');
		return fetch('https://ipwho.is/').then(res => res.json()).then((d)=>{
			console.timeEnd('IpRequest');
			var userLang = navigator.language || navigator.userLanguage; 
	    	return {
	    		sessionIp: (d.ip ? d.ip : '000.000.000'), 
	    		sessionCountry: (d.country_code ? d.country_code : userLang),
	    		userLandingUrl: window.location.href || userLandingUrlAlternative,
  				userInboundUrl: window.referrer || 'NONE',
                status: 'success'
	    	};
	    }).catch(err=>{
	    	console.log(err);
			return {status: 'failed'};
		});
	},

	async getUsetAgent(){ // check what browser and os
		console.time('UserAgent');
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
			console.timeEnd('UserAgent');
			return {sessionBrowser: matchesBrowser, sessionOs: matchesOs, type: type, status: 'success'};
		} catch(err){
			console.log(err);
			return {status: 'failed'};
		};
	},

	async getSession(d, device){ // get new session from api
		console.time('GetSession');
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
			console.timeEnd('GetSession');
			return res;
		}).catch(err=>{
			console.log(err, d, device);
			return {status: 'failed'};
		});
	},

	async checkSession(d){ // check session
		console.time('CheckSession');
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
			console.timeEnd('CheckSession');
			return res;
		}).catch(err=>{
			console.log(err);
			return {status: 'failed'};
		});
	},

	async getLocalSession(){ // check if local session cookie exists
        console.time('CheckLocaleSession');
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
                if(sessionDate > (date - 90 * 24 * 60 * 60 * 1000)){ // check timestamp no older then 90 days
                    sessionObj['status'] = 'failed';
					console.timeEnd('CheckLocaleSession');
                    return sessionObj;
                } else { // all check pass, no need to request new session
                    sessionObj['status'] = 'success';
                    part.forEach(item=>{
                        const objItem = item.split('=');
                        const name = objItem[0].replace(' ', '');
                        const value = objItem[1];
                        sessionObj[name] = value;
                    });
					console.timeEnd('CheckLocaleSession');
                    return sessionObj;
                };
            };
        } catch(err){
        	console.log(err);
            return {status: 'failed'};
        };
	},

	async setLocalSession(data, network){ // save or update session to local cookie
		try{
			const {sessionKey, dateCreated, userKey} = data;
			const {sessionIp, sessionCountry} = network;
			document.cookie = `sessionKey=${sessionKey}&creationDate=${dateCreated}&userKey=${userKey}&ip=${sessionIp}&country=${sessionCountry}`;
		} catch(err){
			console.log(err, data, network);
		};
	},

	async getScores(){ // get list of "getresult" items from DOM and return first 8
		console.time('GetScores');
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
				console.timeEnd('GetScores');
				// ads are set, now check if getResult true/false
		   		return {scores: scores, status: 'success'};
		   	} else {
				console.timeEnd('GetScores');
				getResultListener();
		   		return {scores: '', status: 'failed'};
		   	};
		} catch(err){
			console.log(err);
			return {scores: '', status: 'failed'};
		};
	},

	async getGlobal(){ // request for global ads
		console.time('GlobalRequest');
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
			console.timeEnd('GlobalRequest');
			return res;
		}).catch(err=>{
			console.log(err);
			return {status: 'failed'};
		});
	},

	async getContextual(d){ // request for contextual ads
		console.time('ContextualRequest');
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
			console.timeEnd('ContextualRequest');
			return res;
		}).catch(err=>{
			console.log(err);
			return {status: 'failed'};
		});
	},

	async prepareImg(selected, raw, session, type, last){ // create image objects
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
	        "impressionsBackfill": "FALSE", //*** ?
	        "backfillKey": "" //*** ?
	    };

	    this.handleImages(img, load, type);
	},

    async handleImages(image, load, t){ // all logic for placing images
		console.time('HandleImage');
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
		console.timeEnd('HandleImage');
    },

    async registerImpression(d, e, t, i, r){ // if image loaded start the impression registration function
		console.time('RegisterImpression');
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
			console.timeEnd('RegisterImpression');
        }).catch(err=>{
        	console.log(err);
            return registerImpression(d, e, t, i, r);
        });
    },

    async registerClick(d, t){ // register click on ad
        const endpoint = (t.type == 'contextual' ? '/contextual/isipho' : '/global/isipho'); // check what endpoint to use global/contextual
        return fetch(apiUrl+endpoint, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(d)
        }).then(res=>res.json()).then(res=>{
            // we don't do anything with the response of click register
        }).catch(err=>{
        	console.log(err);
            return registerClick(d, t);
        });
    },

    async generateRandom(min, max){ // get random number within range
	    let difference = max - min;
	    let rand = Math.random();
	    rand = Math.floor(rand * difference);
	    rand = rand + min;
	    return rand;
	},

	async setDisplay(){ // once page loaded get all ad divs and set display:none
		console.time('StartDisplay');
	    const ads = document.querySelectorAll('[id^="fm-"]');
		const device = await this.getUsetAgent();
	    ads.forEach(item=>{
	        item.style.display = 'none';
			item.style.margin = 'auto';
	        if(item.id == 'fm-2' && device.type == 'mobile'){ // add html to fm-2 and fm-4
				setLink()
	        } else if(item.id == 'fm-4' && device.type == 'desktop'){
				setLink()
			};

			function setLink(){
				const link = document.createElement('a'); //  `<a href=”${adsUrl}” target = “_blank”>Advertise With Us</a>`;
	            link.innerText = "Advertise With Us";
	            link.title = "Advertise With Us";
	            link.href = adsUrl;
				link.setAttribute('target', 'blank')
				link.style.fontSize = '11px';
				link.style.display = 'block';
				link.style.textAlign = 'center';
	            item.after(link);
			};
	    });
		console.timeEnd('StartDisplay');
	},

	async start(){ // Start the process after DOM loading ready and check if test mode
		console.time('Start');
		const device = await this.getUsetAgent();
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
		console.timeEnd('Start');
	},

	async global(d, s, t){ // handle global ads
		console.time('StartGlobal');
	    const {globalDisplay} = d;
	    for(let i = 1; i < 5; i++){ // loop trough images from result
	        if(globalDisplay[i] != 'no available advert'){
				console.log('Found ads')
	            const item = globalDisplay[i];
				if(t.type == 'mobile' && i < 3){
					this.prepareImg(item, d, s, {type: 'global'});
				} else if(t.type == 'desktop' && i > 2){
					this.prepareImg(item, d, s, {type: 'global'});
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
		            this.prepareImg(item, d, s, {type: 'global'}, pikName);
	        	};
	        } else {
	            const item = globalDisplay[5];
	            this.prepareImg(item, d, s, {type: 'global'});
	        };
	    } else if(globalDisplay[5] != 'no available advert'){ // only 5 is available
	        const item = globalDisplay[5];
			this.prepareImg(item, d, s, {type: 'global'});
	    } else if(globalDisplay[6] != 'no available advert') { // only 6 available
	        for(let i = 0; i < 3 ; i++){
				const item = globalDisplay[6][i];
				const pikName = (i == 0 ? '6a' : (i == 1 ? '6b' : '6c'));
				this.prepareImg(item, d, s, {type: 'global'}, pikName);
			};
	    };
		console.timeEnd('StartGlobal');
	},

	async contextual(d, s, t){ // handle contextual ads
		console.time('StartContextual');
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
		console.timeEnd('StartContextual');
	}
};

async function StartAds(){
	key = document.querySelector('#fmg-display').getAttribute('data-publisherKey');
	API.setDisplay(); // first set all ad div to display none and add extra html
	API.start(); // start without check mode
};
StartAds();

// making a switch to detect if we have gotten the getresult on start or not
// if not, we listen to DOM changes if we detect that the getresult has been added StartAds()

// if we have loaded global ads, meaning we do not have getresults, then we enable the MutationObserver
// each time we detect a mutation in the DOM we check if the DOM contains the element with id of the getresult form.
// if we detect that the form has been added we start the process from begin "StartAds();"

function getResultListener(){
	MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

	const observer = new MutationObserver((mutations, observer)=>{
		const getResult = document.querySelector('#maintableform');
		console.log('change', !getResult)
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
};


// listen to click event from specific button
const button = document.querySelector('#bigbuttonNext');

if(button){
	button.addEventListener('click', ()=>{
		StartAds();
	});
};


// interval to restart the ads function from start every 60 seconds - 60000 miliseconds
setInterval(()=>{
	const exists = document.querySelectorAll('[title="Advertise With Us"]'); // find all existing ads urls and remove before start over
	if(exists){
		exists.forEach(item=>{
			item.remove();
		});
	};
    StartAds();
}, 60000); // set miliseconds