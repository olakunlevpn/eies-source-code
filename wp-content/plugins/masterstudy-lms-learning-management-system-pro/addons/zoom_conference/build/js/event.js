var meetingConfig = {
	appKey: API_KEY, // Use appKey for SDK 4.0.7
	secretKey: SECRET_KEY,
	meetingNumber: meeting_id,
	userName: username,
	passWord: meeting_password,
	leaveUrl: leaveUrl,
	role: 0, //0-Attendee,1-Host,5-Assistant
	userEmail: email,
	lang: lang,
	signature: "",
	china: 0,//0-GLOBAL, 1-China
	webEndpoint: "zoom.us" // Add webEndpoint for init
};

// For v4.0.7, we need to generate JWT signature server-side or use a proper JWT library
// For now, let's use a simplified approach - the signature should be generated server-side
// This is a placeholder - you should implement proper JWT generation on your server
meetingConfig.signature = ""; // This should be generated server-side

// Use checkFeatureRequirements for SDK 4.0.7
console.log(JSON.stringify(ZoomMtg.checkFeatureRequirements()));

// it's option if you want to change the MeetingSDK-Web dependency link resources. setZoomJSLib must be run at first
// ZoomMtg.setZoomJSLib("https://source.zoom.us/{VERSION}/lib", "/av"); // default, don't need call it
if (meetingConfig.china)
	ZoomMtg.setZoomJSLib("https://jssdk.zoomus.cn/4.0.7/lib", "/av"); // china cdn option

ZoomMtg.preLoadWasm();
ZoomMtg.prepareWebSDK();

// Generate JWT signature via AJAX for SDK 4.0.7+ (following official example)
async function getSignature(meetingNumber, role) {
	try {
		const formData = new FormData();
		formData.append('action', 'mslms_generate_zoom_signature');
		formData.append('nonce', window.mslms_zoom_nonce || '');
		formData.append('app_key', meetingConfig.appKey);
		formData.append('app_secret', meetingConfig.secretKey);
		formData.append('meeting_number', meetingNumber);
		formData.append('role', role);
		formData.append('video_webrtc_mode', 1); // Enable WebRTC mode
		
		const response = await fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		});
		
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		
		const data = await response.json();
		console.log('Signature response:', data);
		
		if (data.success) {
			return data.data.signature;
		} else {
			throw new Error(data.data || 'Failed to generate signature');
		}
	} catch (error) {
		console.error('Signature generation error:', error);
		throw error;
	}
}

function beginJoin(signature) {
	// Use SDK 4.0.7 approach with error handling
	ZoomMtg.i18n.load(meetingConfig.lang);
	ZoomMtg.i18n.onLoad(function () {
		// Add error handling for init
		try {
			ZoomMtg.init({
				leaveUrl: meetingConfig.leaveUrl,
				webEndpoint: meetingConfig.webEndpoint,
				disableCORP: !window.crossOriginIsolated,
				externalLinkPage: "./externalLinkPage.html",
				success: function () {
					console.log(meetingConfig);
					console.log("signature", signature);
					
					ZoomMtg.join({
						meetingNumber: meetingConfig.meetingNumber,
						userName: meetingConfig.userName,
						signature: signature,
						// Don't include sdkKey in join for SDK 4.0.7
						userEmail: meetingConfig.userEmail,
						passWord: meetingConfig.passWord,
						success: function (res) {
							console.log(username);
							console.log("join meeting success");
							console.log("get attendeelist");
							ZoomMtg.getAttendeeslist({});
							ZoomMtg.getCurrentUser({
							success: function (res) {
								console.log("success getCurrentUser", res.result.currentUser);
							},
							});
						},
						error: function (res) {
							console.log("Join error:", res);
						},
					});
				},
				error: function (res) {
					console.log("Init error:", res);
				},
			});
		} catch (error) {
			console.log("Init caught error:", error);
			// Try to join anyway
			console.log("Attempting to join despite init error...");
			ZoomMtg.join({
				meetingNumber: meetingConfig.meetingNumber,
				userName: meetingConfig.userName,
				signature: signature,
				userEmail: meetingConfig.userEmail,
				passWord: meetingConfig.passWord,
				success: function (res) {
					console.log("Join success despite init error");
				},
				error: function (res) {
					console.log("Join error:", res);
				},
			});
		}
	
	ZoomMtg.inMeetingServiceListener("onUserJoin", function (data) {
		console.log("inMeetingServiceListener onUserJoin", data);
	});
	
	ZoomMtg.inMeetingServiceListener("onUserLeave", function (data) {
		console.log("inMeetingServiceListener onUserLeave", data);
	});
	
	ZoomMtg.inMeetingServiceListener("onUserIsInWaitingRoom", function (data) {
		console.log("inMeetingServiceListener onUserIsInWaitingRoom", data);
	});
	
	ZoomMtg.inMeetingServiceListener("onMeetingStatus", function (data) {
		console.log("inMeetingServiceListener onMeetingStatus", data);
	});
	
	});
}

// Generate JWT signature and start the meeting (following official example)
async function startMeeting() {
	try {
		const signature = await getSignature(meetingConfig.meetingNumber, meetingConfig.role);
		console.log('JWT signature generated successfully:', signature);
		beginJoin(signature);
	} catch (error) {
		console.error('Failed to generate JWT signature:', error);
		// Fallback: try with empty signature (may not work in v4.0.7+)
		console.log('Attempting to join with empty signature...');
		beginJoin('');
	}
}

// Start the meeting
startMeeting();

