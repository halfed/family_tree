jQuery(document).ready(function() {
	
	if(jQuery("#family-container").length > 0){
        if(data.membersInfo.length > 0){
            displayMembers(data.membersInfo, true, false, true);
        }
                
        if(data.parentInfo.length > 0 && data.childrenInfo.length > 0) {
            displayMembers(data.parentInfo, false);
            displayMembers(data.childrenInfo, true, true);
        }
    }
	
	showSpouseFields();
    
    var getPhotoParam = getCookieValue("userPhotoCookie");
    
    if(getPhotoParam !== "" && jQuery(".success").length) {
        jQuery("#savedMemberPhoto").attr('src', "http://familytree.deslogefamily.com/family-tree/modx/" + getPhotoParam).css("display", "block");
    }
    
    if(jQuery("#savedMemberPhoto").attr('src') !== "") {
        jQuery("#savedMemberPhoto").css("display", "block");
    }
    
    jQuery(".register_firstName").on("keyup keypress blur change", function() {
        if(jQuery(this).val() !== "") {
            jQuery(".register_lastName").prop("disabled", false);
        }
    });
    jQuery(".register_lastName").on("keyup keypress blur change", function() {
        if(jQuery(this).val() !== "") {
            jQuery(".register_dobMonth").prop("disabled", false);
        }
    });
    jQuery(".register_dobMonth").on("keyup blur change", function() {
        if(jQuery(this).val() !== "") {
            jQuery(".register_dobDay").prop("disabled", false);
        }
    });
    jQuery(".register_dobDay").on("keyup blur change", function() {
        if(jQuery(this).val() !== "") {
            jQuery(".register_dobYear").prop("disabled", false);
        }
    });
    jQuery("#checkMember").on("click", function(e) {
        e.preventDefault();
        var optionalMembers;
        //var firstName = jQuery(".register_firstName").val().substring(0, 3);
        var firstName = jQuery(".register_firstName").val()
        var lastName = jQuery(".register_lastName").val();
        var dobMonth = jQuery(".register_dobMonth").val();
        var dobDay = jQuery(".register_dobDay").val();
        var dobYear = jQuery(".register_dobYear").val();
        if(firstName !== "" && lastName !== "" && dobMonth !== "" && dobDay !== "" && dobYear !== "") {
            jQuery.post("http://familytree.deslogefamily.com/family-tree/modx/assets/scripts/getPotentialUser.php", //Required URL of the page on server
                { // Data Sending With Request To Server
                    firstname: firstName,
                    lastname: lastName,
                    dobday: dobDay,
                    dobmonth: dobMonth,
                    dobyear: dobYear
                },
                function(response,status){ // Required Callback Function
                    //console.log("status "+response);
                    jQuery(".se-pre-con").show();
                    if(status === 'success'){
                        $( ".member-results-body" ).empty();
                        optionalMembers  = "";
                        var obj = JSON.parse(response);
                        if(obj != "") {
                            $.each(obj, function () {
                               $.each(this, function (name, value) {
                                  if(value.parent_fName == null) {
                                     value.parent_fName = "";
                                  }
                                  if(value.parent_lName == null) {
                                      value.parent_lName = "";
                                  }
                                  optionalMembers = '<div class="row">\
                                                         <div class="small-3 columns">\
                                                             <a href="#" class="button tiny round m_'+value.member_id+'">Claim This Profile</a>'
                                                             + '</div>\
                                                             <div class="small-3 columns member-results-box">' + value.first_name +
                                                      '&nbsp;' + value.last_name + '</div>\
                                                      <div class="small-3 columns member-results-box">' + 
                                                      value.newDob + '</div>\
                                                      <div class="small-3 columns member-results-box">' + value.parent_fName + '&nbsp;' + value.parent_lName +'</div>\
                                                      </div>';
                                  jQuery(".member-results-body").append(optionalMembers);
                                  
                                  jQuery(".m_"+value.member_id).on("click", function(e) {
                                      e.preventDefault();
                                      jQuery(".register_firstName").val(value.first_name);
                                      jQuery(".register_lastName").val(value.last_name);
                                      var pieces = value.newDob.split("/");
                                      jQuery("#dobMonth").val(pieces[0]);
                                      jQuery("#dobDay").val(pieces[1]);
                                      jQuery("#dobYear").val(pieces[2]);
                                      jQuery(".register-continue").show();
                                      
                                      jQuery('html, body').animate({scrollTop:jQuery(document).height() - jQuery(window).height()}, 'slow');
                                      jQuery(".member-results-container").hide();
                                  });
                                  jQuery(".divider-container").show();
                               });
                            });
                            jQuery(".profile-results").show();
                            jQuery(".profile-results-body").show();
                            jQuery(".continue-register").on("click", function(e) {
                                e.preventDefault();
                                jQuery(".register-continue").show();
                                jQuery('html, body').animate({scrollTop:jQuery(document).height() - jQuery(window).height()}, 'slow');
                                jQuery(".member-results-container").hide();
                            });
                        }
                        else {
                            jQuery( ".member-results-body" ).empty();
                            jQuery(".profile-results").hide();
                            optionalMembers += '<div class="row"><div class"small-6 columns"><p>No matching profile results were found. Please click <a href="#" class="continue-register">here</a> to continue with the registration process. Once registered and signed in you will be able to continue updating your profile and view the family tree.</p></div></div>';
                            jQuery(".divider-container").hide();
                            jQuery(".member-results-body").append(optionalMembers);
                            jQuery(".continue-register").on("click", function(e) {
                                e.preventDefault();
                                jQuery(".register-continue").show();
                                jQuery('html, body').animate({scrollTop:jQuery(document).height() - jQuery(window).height()}, 'slow');
                                jQuery(".member-results-container").hide();
                            });
                        }
                        
                        jQuery(".member-results-container").show();
                        jQuery('html,body').animate({scrollTop:0},0);
                        jQuery(".se-pre-con").hide();
                    }
                    
                });
           }
        
    });

    displayParentBox();
    
    jQuery("#more_info").on("click", function() {
        jQuery(".additional-info").toggle();
    });
    
    /*-- VALIDATION STARTS HERE --*/
    jQuery(".reset-password").validate({
        debug: false,
        ignore: ".ignore",
        rules: {
            // simple rule, converted to {required:true}
            password_old: "required",
            // compound rule
            password_new: {
              required: true,
              minlength: 2
            },
            password_new_confirm: {
              equalTo: "#password_new"
            }
        },
        messages: {
            password_new: {
              required: "This field is required",
              password_new: "Passwords must have at least 2 characters"
            },
            password_new_confirm: {
                 equalTo: "Your password and confirm password do not match"
            }
        },
        submitHandler: function(form) {
            // do other things for a valid form
            form.submit();
        }
    });
    
    jQuery(".loginLoginForm").validate({
        rules: {
            username: "required",
            password: "required"
        },
        submitHandler: function(form) {
            // do other things for a valid form
            form.submit();
        }
    });
    
    jQuery("#forgot-password").validate({
        rules: {
            username: "required",
            email: {
                required: true,
                email: true
            }
        },
        submitHandler: function(form) {
            // do other things for a valid form
            form.submit();
        }
    });
    
    jQuery("#registrationForm").validate({
       rules: {
           username: "required",
           password: {
               required: true,
               minlength: 2
           },
           password_confirm: {
               equalTo: "#password"
               
           },
           email: {
                required: true,
                email: true
            }
       },
        submitHandler: function(form) {
            var registerDob = new GetBirthDates('dobDay', 'dobMonth', 'dobYear', 'dob');
            registerDob.convertBirthDates();
            
            var registerFullName = new GetFullName('firstName', 'lastName', 'fullname');
            registerFullName.concatName();
            
            form.submit();
        },
       messages: {
           password_confirm: {
                 equalTo: "Your password and confirm password do not match"
            }
       }
    });
    
    jQuery("#emailAdminForm").validate ({
        rules: {
            firstname: "required",
            lastname: "required",
            email: {
                required: true,
                email: true
            }
        },
        submitHandler: function(form) {
            // do other things for a valid form
            form.submit();
        }
    });
    
    jQuery("#memberInput").validate({
       rules: {
           firstName: "required",
           lastName: "required",
           dobMonth: "required",
           dobDay: "required",
           dobYear: "required",
           memberGeneration: "required",
           email: {
               required: true,
               email: true
           }
       },
        submitHandler: function(form) {
            var birthDate = new GetBirthDates('dobDay', 'dobMonth', 'dobYear', 'dob');
            birthDate.convertBirthDates();
            
            var deathDate = new GetBirthDates('dodDay', 'dodMonth', 'dodYear', 'dod');
            deathDate.convertBirthDates();
            
            var parentBirthDate = new GetBirthDates('parentDobDay', 'parentDobMonth', 'parentDobYear', 'parentDob');
            parentBirthDate.convertBirthDates();
            
            var childBirthDate = new GetBirthDates('childDobDay', 'childDobMonth', 'childDobYear', 'childDob');
            childBirthDate.convertBirthDates();
            
            var spouseBirthDate = new GetBirthDates('spouseDobDay', 'spouseDobMonth', 'spouseDobYear', 'spouseDob');
            spouseBirthDate.convertBirthDates();
            
            
            form.submit();
        }
    });
   
    jQuery("#searchForm").validate({
        debug: false,
        errorPlacement: function(error, element) {
            error.appendTo(".searchMessage");
        },
        groups: {
            search_member: "searchFirstName searchLastName memberGeneration currentState searchCollege searchOccupation"
        },
         rules: {
             searchFirstName: {
                 require_from_group: [1, ".search_member"]
             },
             searchLastName: {
                 require_from_group: [1, ".search_member"]
             },
             memberGeneration: {
                 require_from_group: [1, ".search_member"]
             },
             currentState: {
                 require_from_group: [1, ".search_member"]
             },
             searchCollege: {
                 require_from_group: [1, ".search_member"]
             },
             searchOccupation: {
                 require_from_group: [1, ".search_member"]
             }
         },
         submitHandler: function (form, event) {
            searchMember();
            event.preventDefault();
            return false;
        },
        messages: {
            search_member: {
                required: "Please fill at least one field."
            }
        }
    });
    jQuery.validator.addMethod("require_from_group", jQuery.validator.methods.require_from_group, 'Please fill out at least one field.');
});

function searchMember() {
    var firstName = jQuery("#searchFirstName").val()
    var lastName = jQuery("#searchLastName").val();
    var currentState = jQuery("#currentState option:selected").val();
    var generation = jQuery("#memberGeneration option:selected").val();
    var college = jQuery("#searchCollege").val();
    var occupation = jQuery("#searchOccupation").val();
    var memberResults;
    
    jQuery(".member-result").remove();
    
    jQuery.post("http://familytree.deslogefamily.com/family-tree/modx/assets/scripts/memberSearchProfile.php", //Required URL of the page on server
        { // Data Sending With Request To Server
            firstname: firstName,
            currentstate: currentState,
            generation: generation,
            lastname: lastName,
            college: college,
            occupation: occupation
        },
        function(response,status){ // Required Callback Function
            if(status === 'success'){
                
                var obj = JSON.parse(response);
                if(obj !== "") {
                    $.each(obj, function () {
                        $.each(this, function (name, value) {
                            
                            removeMemberNullValues(value);
                            
                            memberResults  = "";
                            memberResults = '\
                                <div class="row member-result">\
                                    <div class="large-1 columns">\
                                        <div class="individual-member">\
                                            <div class="member-content-top"></div>\
                                                <div class="content">\
                                                    <a href="#" data-reveal-id="member_'+value.member_id+'" id="this_member_'+value.member_id+'">\
                                                        <div class="member-image-display" style="background-image:url(assets/images/profile/' + (value.member_image !== "" ? value.member_image : "image-placeholder_large.png") + ')"></div>\
                                                    </a>\
                                                </div>\
                                            <div class="content-bottom"></div>\
                                        </div>\
                                    </div>\
                                    <div class="large-8 left columns member-inputs">\
                                        <p>- ' + value.first_name + ' ' + value.last_name + '</p><p>- ' + value.college + '</p><p>- ' + value.occupation + '</p>\
                                    </div>\
                                </div>';
                            memberResults += setUserModal(value);//value is the JSON object
                            jQuery(".searchResults").append(memberResults);
                            jQuery("#this_member_"+value.member_id).click(function (e) {
                                e.preventDefault();
                                jQuery("#member_"+value.member_id).foundation('reveal', 'open');
                            });
                        });
                    });
                }
            }
        }
    );
}

function displayParentBox() {
    jQuery('#bornIntoFamily').change(function() {
        if(this.checked) {
            jQuery(".parent-box-container").show();
        }
        else {
          jQuery(".parent-box-container").hide();  
        }
    });
    
    if(jQuery('#bornIntoFamily').is(':checked')) {
        jQuery(".parent-box-container").show();
    }
}

function GetBirthDates (dobDay, dobMonth, dobYear, dateId) {
    this.dobDay = jQuery("#" + dobDay).val();
    this.dobMonth = jQuery("#" + dobMonth).val();
    this.dobYear = jQuery("#" + dobYear).val();
    this.dateId = dateId;
}

GetBirthDates.prototype.convertBirthDates = function() {
  this.fullDate = this.dobYear + "-" + this.dobMonth + "-" + this.dobDay;
  jQuery("#" + this.dateId).val(this.fullDate);
}

function GetFullName (firstname, lastname, fullnameId) {
  this.firstName = jQuery("#" + firstname).val();
  this.lastName = jQuery("#" + lastname).val();
  this.fullNameId = fullnameId;
}

GetFullName.prototype.concatName = function() {
  this.fullName = this.firstName +" "+ this.lastName;
  jQuery("#" + this.fullNameId).val(this.fullName);
}

function showSpouseFields() {
    if(jQuery('#haveSpouse').is(':checked')){
        jQuery("#spouseContainer").show();
    }
    jQuery('#haveSpouse').change(function() {
        if ($(this).is(':checked')) {
            jQuery("#spouseContainer").show();
        } else {
            jQuery("#spouseContainer").hide();
        }
    });
}

function displayMembers(membersData, displayChildren, singleGen, hideChildrenFirstGen) {
    var memberCount = 0;
        
    if(!displayChildren){
        content = "";
        content += '<div class="clearfix row"><div class="small-10 small-centered columns"><nav class="breadcrumbs"><a class="backButton" onclick="goBack()">Previous</a><a href="/family-tree/modx/index.php?id=1">Back to the top</a></nav></div></div><div class="row"><div class="columns large-12"><div class="individual-member-container">';
        
        
    }
    jQuery.each (membersData, function (index, b) {
        if(displayChildren){
            content = "";
        }
        
        removeNullValues(b,{
            email: "email",
            phone: "phone",
            currentCity: "currentCity",
            currentState: "currentState",
            college: "college",
            occupation: "occupation",
            address: "address",
            facebook: "facebook",
            linkedin: "linkedin",
            suffix: "suffix",
            nickName: "nickName",
            birthCity: "birthCity",
            birthState: "birthState",
            website: "website",
            aboutMe: "aboutMe"
        });
                        
        if(b.spouseMemberId !== ""){
            if(b.spouseMemberId !== null && displayChildren) {
                  removeNullValues(b, {
                  spouseMName: "spouseMName",
                  spouseEmail: "spouseEmail",
                  spousePhone: "spousePhone",
                  spouseCurrentCity: "spouseCurrentCity",
                  spouseCurrentState: "spouseCurrentState",
                  spouseCollege: "spouseCollege",
                  spouseOccupation: "spouseOccupation",
                  spouseAddress: "spouseAddress",
                  spouseFacebook: "spouseFacebook",
                  spouseLinkedin: "spouseLinkedin",
                  spouseSuffix: "spouseSuffix",
                  spouseNickName: "spouseNickName",
                  spouseHomeCity: "spouseHomeCity",
                  spouseHomeState: "spouseHomeState",
                  spouseWebsite: "spouseWebsite",
                  spouseAboutMe: "spouseAboutMe"
                });
            }
        }
        if(!displayChildren) {
            content += '<div class="individual-member">\
                            <div class="content-top '+ b.familyStatus +'"></div>\
                            <div class="content">\
                                <a href="#" data-reveal-id="member'+b.memberId+'">\
                                    <div class="member-image-display" style="background-image:url(assets/images/profile/'+ b.memberImage +');"></div>\
                                    <div class="member-name">'+b.firstName+'<br/>'+b.lastName+'</div>\
                                </a>\
                            </div>\
                            <div class="content-bottom"></div>\
                        </div>';
        }else {
            content += '<div class="'+(singleGen ? "individual-member-child-container" : "individual-member-container")+'">\
                            <div class="individual-member '+((b.hasChildren && displayChildren && b.spouseMemberId === null) ? "individual-member-single-parent" : "") +'">\
                                <div class=" content-top '+((b.hasChildren && displayChildren && b.spouseMemberId === null) ? "content-top-single-parent" : "")+' '+  (hideChildrenFirstGen ? b.familyStatus : b.familyChildStatus)+'"></div>\
                                <div class="content '+((b.hasChildren && displayChildren && b.spouseMemberId === null) ? "content-single-parent" : "")+'">\
                                    <a href="#" data-reveal-id="member'+b.memberId+'">\
                                        <div class="member-image-display" style="background-image:url(assets/images/profile/'+ b.memberImage +');"></div>\
                                        <div class="member-name">'+b.firstName+'<br/>'+b.lastName+'</div>\
                                    </a>\
                                </div>\
                            <div class="content-bottom '+((b.hasChildren && displayChildren && b.spouseMemberId === null) ? "content-bottom-single-parent" : "")+'"></div>\
                        </div>';
        }
        if(b.spouseMemberId !== null && displayChildren) {
            content += '<div class="individual-member spouseContainer">\
                            <div class="content-top '+ b.familySpouseStatus +'"></div>\
                            <div class="content">\
                                <a href="#" data-reveal-id="member'+b.spouseMemberId+'">\
                                    <div class="member-image-display" style="background-image:url(assets/images/profile/'+b.spouseMemberImage+');"></div>\
                                    <div class="member-name">'+b.spouseFName+'<br/>'+b.spouseLName+'</div>\
                                </a>\
                            </div>\
                            <div class="content-bottom"></div>\
                        </div>';
        }
        if(b.hasChildren && displayChildren){
            content += '<a href="'+b.familyUrl+'"><div id="triangle_'+memberCount+'" class="triangle clearfix"></div><div class="seeMore '+((b.hasChildren && displayChildren && b.spouseMemberId === null) ? "seeMore-single-parent" : "")+'">Click to see more</div></a>';
            
        }
        
        if(displayChildren){
            content += '</div>';
        }
        content += '<div id="member'+b.memberId+'" class="reveal-modal row" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">\
                    <div class="columns">\
                    <fieldset>\
                    <legend>Family Member</legend>\
                    <div class="large-3 element-float-left l-margin-right"><image src="assets/images/profile/'+ b.memberImage +'" width="255" height="365"/></div>\
                    <div class="large-7 element-float-left s-margin-top">\
                    <p class="el-small">First Name: '+b.firstName+'</p>\
                    <p class="el-small">Middle Name: '+b.middleName+'</p>\
                    <p class="el-small">Last Name: '+b.lastName+'</p>\
                    <p class="el-small">Suffix: '+b.suffix+'</p>\
                    <p class="el-small">Nick Name: '+b.nickName+'</p>'
                    
                    if(b.dod !== '0000-00-00') {
                        content += '<p class="el-small">Date of Death: '+b.dod+'</p>';
                    }
                    else {
                      content += '<p class="el-small">Age: '+b.dob+'</p>';  
                    }
        content += '<p class="el-small">Home City: '+b.birthCity+'</p>\
                    <p class="el-small">Home State: '+b.birthState+'</p>\
                    <p class="el-small">Email: '+b.email+'</p>\
                    <p class="el-small">Phone: '+b.phone+'</p>\
                    <p class="el-small">Current City: '+b.currentCity+'</p>\
                    <p class="el-small">Current State: '+b.currentState+'</p>\
                    <p class="el-small">College: '+b.college+'</p>\
                    <p class="el-small">Occupation: '+b.occupation+'</p>\
                    <p class="el-small">Facebook: <a href="'+ b.facebook +'" target="_blank">'+b.facebook+'</a></p>\
                    <p class="el-small">LinkedIn: <a href="'+ b.linkedin +'" target="_blank">'+b.linkedin+'</a></p>\
                    <p class="el-small">Website: <a href="'+ b.website +'" target="_blank">'+b.website+'</a></p>\
                    <p class="el-small">About Me:</p><p>'+b.aboutMe+'</p>\
                    </div>\
                    </fieldset>\
                    </div>\
                    <a class="close-reveal-modal" aria-label="Close">&#215;</a>\
                    </div>';
        if(b.spouseMemberId !== "") {
            if((b.spouseMemberId !== null) && displayChildren) {
                content += '<div id="member'+b.spouseMemberId+'" class="reveal-modal row spouse" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">\
                        <div class="columns">\
                        <fieldset>\
                        <legend>Family Member</legend>\
                        <div class="large-3 element-float-left l-margin-right"><img src="assets/images/profile/'+ b.spouseMemberImage +'" width="255" height="365"/></div>\
                        <div class="large-7 element-float-left s-margin-top">\
                        <p class="el-small">First Name: '+b.spouseFName+'</p>\
                        <p class="el-small">Middle Name: '+b.spouseMName+'</p>\
                        <p class="el-small">Last Name: '+b.spouseLName+'</p>\
                        <p class="el-small">Suffix: '+b.spouseSuffix+'</p>\
                        <p class="el-small">Nick Name: '+b.spouseNickName+'</p>\
                        <p class="el-small">Age: '+b.spouseDob+'</p>'
                        if(b.spouseDod !== '0000-00-00') {
                            content += '<p class="el-small">Date of Death: '+b.spouseDod+'</p>';
                        }
                        else {
                          content += '<p class="el-small">Age: '+b.spouseDob+'</p>';  
                        }
                content += '<p class="el-small">Home City: '+b.spouseHomeCity+'</p>\
                        <p class="el-small">Home State: '+b.spouseHomeState+'</p>\
                        <p class="el-small">Email: '+b.spouseEmail+'</p>\
                        <p class="el-small">Phone: '+b.spousePhone+'</p>\
                        <p class="el-small">Current City: '+b.spouseCurrentCity+'</p>\
                        <p class="el-small">Current State: '+b.spouseCurrentState+'</p>\
                        <p class="el-small">College: '+b.spouseCollege+'</p>\
                        <p class="el-small">Occupation: '+b.spouseOccupation+'</p>\
                        <p class="el-small">Facebook: <a href="'+ b.spouseFacebook +'" target="_blank">'+b.spouseFacebook+'</a></p>\
                        <p class="el-small">LinkedIn: <a href="'+ b.spouseLinkedin +'" target="_blank">'+b.spouseLinkedin+'</a></p>\
                        <p class="el-small">Website: <a href="'+ b.spouseWebsite +'" target="_blank">'+b.spouseWebsite+'</a></p>\
                        <p class="el-small">About Me:</p><p>'+b.spouseAboutMe+'</p>\
                        </div>\
                        </fieldset>\
                        </div>\
                        <a class="close-reveal-modal" aria-label="Close">&#215;</a>\
                        </div>';
            }
        }
        if(displayChildren) {
            jQuery(content).appendTo("#family-container");
            jQuery("#triangle_"+memberCount).hover( function() {
                jQuery(this).next().show();
                }, function() { 
                     jQuery(this).next().hide();
            });
        }
        memberCount++;
    });
    if(!displayChildren){
        content += '</div></div></div>';
        jQuery(content).appendTo("#family-container");
        
    }
}

function setUserModal(value) {//value is the JSON object
    var content = "";
    
    removeMemberNullValues(value);
    
    content += '<div id="member_'+value.member_id+'" class="reveal-modal row" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">\
               <div class="columns">\
                    <fieldset>\
                         <legend>Family Member</legend>\
                         <div class="large-3 element-float-left l-margin-right"><image src="assets/images/profile/'+ (value.member_image !== "" ? value.member_image : "image-placeholder_large.png") +'" width="255" height="365"/></div>\
                         <div class="large-7 element-float-left s-margin-top">\
                              <p class="el-small">First Name: '+value.first_name+'</p>\
                              <p class="el-small">Middle Name: '+value.middle_name+'</p>\
                              <p class="el-small">Last Name: '+value.last_name+'</p>\
                              <p class="el-small">Suffix: '+value.suffix+'</p>\
                              <p class="el-small">Nick Name: '+value.nick_name+'</p>'
     
                              if(value.dod !== '0000-00-00') {
                                  content += '<p class="el-small">Date of Death: '+value.dod+'</p>';
                              }
                              else {
                                content += '<p class="el-small">Age: '+value.age+'</p>';  
                              }
content += '                  <p class="el-small">Home City: '+value.home_city+'</p>\
                              <p class="el-small">Home State: '+value.home_state+'</p>\
                              <p class="el-small">Email: '+value.email+'</p>\
                              <p class="el-small">Phone: '+value.phone+'</p>\
                              <p class="el-small">Current City: '+value.current_city+'</p>\
                              <p class="el-small">Current State: '+value.current_state+'</p>\
                              <p class="el-small">College: '+value.college+'</p>\
                              <p class="el-small">Occupation: '+value.occupation+'</p>\
                              <p class="el-small">Facebook: <a href="'+ value.facebook +'" target="_blank">'+value.facebook+'</a></p>\
                              <p class="el-small">LinkedIn: <a href="'+ value.linkedIn +'" target="_blank">'+value.linkedIn+'</a></p>\
                              <p class="el-small">Website: <a href="'+ value.website +'" target="_blank">'+value.website+'</a></p>\
                              <p class="el-small">About Me:</p><p>'+value.about_me+'</p>\
                         </div>\
                    </fieldset>\
               </div>\
               <a class="close-reveal-modal" aria-label="Close">&#215;</a>\
          </div>';
          
    return content;
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.readAsDataURL(input.files[0]);
    }
}

var inputs = document.querySelectorAll( '.inputfile' );
Array.prototype.forEach.call( inputs, function( input )
{
    var label    = input.nextElementSibling,
        labelVal = label.innerHTML;

    input.addEventListener( 'change', function( e )
    {
        var fileName = '';
        if( this.files && this.files.length > 1 ) {
            fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
        }
        else {
            fileName = e.target.value.split( '\\' ).pop();
        }
        if( fileName ) {
            label.querySelector( 'span' ).innerHTML = fileName;
            jQuery("#memberImage").val("");
            jQuery("#memberImage").val(fileName);
            
        }
        else {
            label.innerHTML = labelVal;
        }
    });
});

//Get Cookie Value
function getCookieValue(cookieName) {
	var cookieVal = document.cookie;
	var thisCookieStartsAt = cookieVal.indexOf(" " + cookieName + "=");

	if(thisCookieStartsAt == -1) {
		thisCookieStartsAt = cookieVal.indexOf(cookieName + "=");
	}
	if(thisCookieStartsAt == -1) {
		cookieVal = null;
	}
	else {
		thisCookieStartsAt = cookieVal.indexOf("=", thisCookieStartsAt) + 1;
		var thisCookieEndsAt = cookieVal.indexOf(";", thisCookieStartsAt);
		if(thisCookieEndsAt == -1) {
			thisCookieEndsAt = cookieVal.length;
		}
		cookieVal = unescape(cookieVal.substring(thisCookieStartsAt, thisCookieEndsAt));
	}

	return cookieVal;
}


function goBack() {
    window.history.back();
}
        
        
function goForward() {
    window.history.forward();
}

//We need this for MODX JSON values
function removeNullValues(memberObject, toTest) {
  for (var prop in toTest) {
      if (memberObject[prop] === null) {
          memberObject[prop] = "";
      }
  }
}

function removeMemberNullValues(objects) {
    for(var el in objects) {
        if(objects[el] === null) {
            objects[el] = "";
        }
    }
}


