var config = {
    apiKey: "AIzaSyC3FpISPgUilSgSmpm-pjOUPlRp8nYwm5Q",
    authDomain: "pause-fb19e.firebaseapp.com",
    databaseURL: "https://pause-fb19e.firebaseio.com",
    projectId: "pause-fb19e",
    storageBucket: "pause-fb19e.appspot.com",
    messagingSenderId: "714359295912"
};
firebase.initializeApp(config);
check_if_logged_in();

function goto_main_page()   {
    window.location.href = "/index.html";
}

function check_if_logged_in()   {
    debugger;
    setTimeout(function(){
        if(firebase.auth().currentUser == null)    {
            goto_main_page();
        }
    }, 300);
}

$("#sign-out").click(function(){
    firebase.auth().signOut();
    goto_main_page();
});
