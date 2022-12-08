/**************************************************************************
 * Alp-Chat configurations
 * ************************************************************************/
switch (ALP_SERVER) {
    case "localhost":
        var FIREBASE_CONFIG = {
            apiKey: "AIzaSyC8HgrhDACWkvBmiRtE-wNXOvTl80IXYFc",
            authDomain: "alp-chat-local.firebaseapp.com",
            databaseURL: "https://alp-chat-local-default-rtdb.firebaseio.com",
            projectId: "alp-chat-local",
            storageBucket: "alp-chat-local.appspot.com",
            messagingSenderId: "982339270839",
            appId: "1:982339270839:web:922df8c0a6113b9af33532",
            measurementId: "G-D0QWQ1FZLZ",
        };
        break;
    case "alp3":
        var FIREBASE_CONFIG = {
            apiKey: "AIzaSyC3Mc-IzfnCWGVw9f_pO1ZKO2B9GnIMjqU",
            authDomain: "alp-chat-alp3.firebaseapp.com",
            databaseURL: "https://alp-chat-alp3-default-rtdb.firebaseio.com",
            projectId: "alp-chat-alp3",
            storageBucket: "alp-chat-alp3.appspot.com",
            messagingSenderId: "695240121582",
            appId: "1:695240121582:web:fe8fdb03a7cafa46e267ff",
            measurementId: "G-V89YV704F1",
        };
        break;
    case "alpweb":
        var FIREBASE_CONFIG = {
            apiKey: "AIzaSyCBum7WlPFe3xxs7lx9joPUB-qrocOaDF0",
            authDomain: "alp-web-chat.firebaseapp.com",
            databaseURL: "https://alp-web-chat-default-rtdb.firebaseio.com",
            projectId: "alp-web-chat",
            storageBucket: "alp-web-chat.appspot.com",
            messagingSenderId: "1060906848738",
            appId: "1:1060906848738:web:782de8fe38fb1cad9ee9bb",
            measurementId: "G-1XB9741FLB",
        };
        break;
    default:
        var FIREBASE_CONFIG = {
            apiKey: "AIzaSyCyBkW8FxV_stSJXX4m3AoQzFFqvH7F5-I",
            authDomain: "alpchatroom.firebaseapp.com",
            databaseURL: "https://alpchatroom-default-rtdb.firebaseio.com",
            projectId: "alpchatroom",
            storageBucket: "alpchatroom.appspot.com",
            messagingSenderId: "759927930941",
            appId: "1:759927930941:web:298995317b9309510b2a54",
            measurementId: "G-SF13FWCWRL",
        };
}

/****** Start Chat Server URl configurations ******************************/
var ALP_CHAT_BASE_URL = ALP_CHAT_BASE_URL; // <- This variable defined into app.blade.php
/***** End Chat Server URl configurations *********************************/

/***** Start FireBase Initialization Configurations ***********************/
// Initialize the firebase App
firebase.initializeApp(FIREBASE_CONFIG);

// Initialize the firebase database
var database = firebase.database();

// Initialize the firebase storage
var storageRef = firebase.storage().ref();
