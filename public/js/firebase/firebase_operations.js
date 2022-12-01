function generateMobileNumber() {
    var MobileNumber = Math.floor(Math.random() * 90000000) + 10000000;
    var lastNumber = new Date().getMilliseconds();
    return "+852" + MobileNumber.toString();
}

function AddUserFirebase(data){
    var PhoneNumber = generateMobileNumber();
    var alp_chat_id = checkUserNumber(data,PhoneNumber);
    return alp_chat_id;
    
}

/**
 * USE : Add new user into firebase database
 */
function addUser(data) {
    var PhoneNumber = data.alp_chat_user_id;
    if (PhoneNumber == "") {
        $.ajax({
            url: BASE_URL + "/get-user-info",
            type: "GET",
            async: false,
            data: {
                uid: data.id,
            },
            success: function (response) {
                if (response.data.length != 0) {
                    PhoneNumber = response.data.alp_chat_user_id;
                    var CurrentUser = PhoneNumber;
                    var email_id = data.email;
                    var username = email_id;
                    var password = email_id;
                    var d = new Date();
                    var n = d.getTime();
                    var ref = firebase
                        .database()
                        .ref("data/users")
                        .orderByChild("e-mail")
                        .equalTo(data.email);
                    ref.once("value", (snapshot) => {
                        if (snapshot.exists()) {
                            snapshotData = Object.keys(snapshot.val());
                            CurrentUser = snapshotData[0];
                            firebase
                                .database()
                                .ref("data/users/" + CurrentUser)
                                .update({
                                    name: data.name_en,
                                    nameToDisplay: data.name_en,
                                    username: data.name_en,
                                });
                            return CurrentUser;
                        } else {
                            firebase
                                .database()
                                .ref("data/users/" + CurrentUser)
                                .update({
                                    deviceToken: "",
                                    id: PhoneNumber,
                                    name: data.name_en,
                                    nameToDisplay: data.name_en,
                                    online: false,
                                    osType: "web",
                                    profileName: PhoneNumber,
                                    selected: true,
                                    status: "ALP-Chat",
                                    timeStamp: n,
                                    typing: "",
                                    "e-mail": email_id,
                                    username: data.name_en,
                                    password: password,
                                });
                            return PhoneNumber;
                        }
                    });
                }
            },
        });
    } else {
        var CurrentUser = PhoneNumber;
        var email_id = data.email;
        var username = email_id;
        var password = email_id;
        var d = new Date();
        var n = d.getTime();
        var ref = firebase
            .database()
            .ref("data/users")
            .orderByChild("e-mail")
            .equalTo(data.email);
        ref.once("value", (snapshot) => {
            if (snapshot.exists()) {
                snapshotData = Object.keys(snapshot.val());
                CurrentUser = snapshotData[0];
                firebase
                    .database()
                    .ref("data/users/" + CurrentUser)
                    .update({
                        name: data.name_en,
                        nameToDisplay: data.name_en,
                        username: data.name_en,
                    });
                return CurrentUser;
            } else {
                firebase
                    .database()
                    .ref("data/users/" + CurrentUser)
                    .update({
                        deviceToken: "",
                        id: PhoneNumber,
                        name: data.name_en,
                        nameToDisplay: data.name_en,
                        online: false,
                        osType: "web",
                        profileName: PhoneNumber,
                        selected: true,
                        status: "ALP-Chat",
                        timeStamp: n,
                        typing: "",
                        "e-mail": email_id,
                        username: data.name_en,
                        password: password,
                    });
                return CurrentUser;
            }
        });
    }
    return PhoneNumber;
}

/**
 * USE : Add new user into firebase database
 */
function checkUserNumber(data, mobileNumber) {
    var PhoneNumber = generateMobileNumber();
    var CurrentUser = PhoneNumber;
    var email_id = data.email;
    var username = email_id;
    var password = email_id;
    var d = new Date();
    var n = d.getTime();
    var ref = firebase
        .database()
        .ref("data/users")
        .orderByChild("id")
        .equalTo(mobileNumber);
    ref.once("value", (snapshot) => {
        if (snapshot.exists()) {
            checkUserNumber(data, mobileNumber);
        } else {
            firebase
                .database()
                .ref("data/users/" + CurrentUser)
                .update({
                    deviceToken: "",
                    id: PhoneNumber,
                    name: data.name_en,
                    nameToDisplay: data.name_en,
                    online: false,
                    osType: "web",
                    profileName: PhoneNumber,
                    selected: true,
                    status: "ALP-Chat",
                    timeStamp: n,
                    typing: "",
                    "e-mail": email_id,
                    username: data.name_en,
                    password: password,
                });
            return PhoneNumber;
        }
    });
}

/**
 * USE: Add new groups
 */
function addGroup(data) {
    var CurrentUser = data.currentuser;
    var new_group_title = data.new_group_title;
    var searchIDData = data.searchIDData;
    var new_group_description = data.new_group_description;
    var d = new Date();
    var n = d.getTime();
    var userId = "group_" + CurrentUser + "_" + n;
    //searchIDData.push(CurrentUser);
    $("#dreamschat_group_id").val(userId);
    firebase
        .database()
        .ref("data/groups/" + userId)
        .set({
            admin: CurrentUser,
            date: n,
            id: userId,
            image: "",
            name: new_group_title,
            status: new_group_description,
            userIds: searchIDData,
        });
}

/**
 * USE : Delete group members
 */
function deleteGroupMember(data, grpExitUserIds, dreamschat_group_id) {
    var CurrentUsers = "";
    setTimeout(function () {
        var ref = firebase
            .database()
            .ref("data/users")
            .orderByChild("e-mail")
            .equalTo(data.email);
        ref.once("value", (snapshot) => {
            if (snapshot.exists()) {
                snapshotData = Object.keys(snapshot.val());
                CurrentUsers = snapshotData[0];
                var remRef = firebase
                    .database()
                    .ref("data/groups/" + dreamschat_group_id);
                remRef.once("value", function (snapshot) {
                    var userarray = snapshot.val().userIds;
                    if (snapshot.val().grpExitUserIds != undefined) {
                        var grpExitUserIds = snapshot.val().grpExitUserIds;
                    } else {
                        var grpExitUserIds = [];
                    }
                    grpExitUserIds.push(CurrentUsers);
                    remRef.update({
                        grpExitUserIds: grpExitUserIds,
                    });
                });
            }
        });
    }, 1000);
}

/**
 * USE : Delete group into firebase
 */
function DeleteChatGroup(GroupId){
    firebase.database().ref("data/groups/"+GroupId).remove();
}

/**
 * USE : ALP-Chat login after click on the chat icons.
 */
function AutoLoginAlpChat(username, password, language, selectedGroup) {
    window.open(
        ALP_CHAT_BASE_URL +
            "login?username=" +
            encodeURIComponent(username) +
            "&password=" +
            encodeURIComponent(password) +
            "&language=" +
            encodeURIComponent(language) +
            "&selectedGroup=" +
            encodeURIComponent(selectedGroup),
        "_blank"
    );
    return false;
}

/**
 * USE: Add new groups
 */
function checkGroupExists(groupId) {
    var remRef = firebase.database().ref("data/groups/" + groupId);
    remRef.once("value", function (snapshot) {
        if (snapshot.exists()) {
            return "1";
        } else {
            return "0";
        }
    });
}
