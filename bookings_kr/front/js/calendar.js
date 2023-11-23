var Calendar = {
  /**
   * load calendar entries from a JSON web service
   * build the HTML entries and enable drag and drop
   * and in place editing
   */
  config: {
    new_flag: 0,
    booking_types: { 1: "Student", 2: "Extras", 3: "Special" },
    student_level: { "": "---", 1: "L1", 2: "L2", 3: "L3", 4: "Ind" },
    extra_level: { "": "Choose", 1: "Not Available", 2: "Setup / Pickup" },
    lessonType: { "": "Choose", KITE: "KITE", WING: "WING", SUP: "SUP" },
  },

  cancelHTML:
    '<div class="btn-group"><button class="btn" onclick="Calendar.clCancel(this);">NO</button><button class="btn"  onclick="Calendar.cnCancel(this);">YES</button></div>',
  cancelHTMLinstructor:
    '<div class="btn-group"><button class="btn" onclick="Calendar.clCancel_ins(this);">NO</button><button class="btn"  onclick="Calendar.cnCancel_ins(this);">YES</button></div>',
  cancelHTMLspecial:
    '<div class="btn-group"><button class="btn" onclick="Calendar.clCancel_special(this);">NO</button><button class="btn"  onclick="Calendar.cnCancel_special(this);">YES</button></div>',
  redoHTML:
    '<div class="btn-group"><button class="btn" onclick="Calendar.cancel_redo(this);">NO</button><button class="btn"  onclick="Calendar.accept_redo(this);">YES</button></div>',
  slDropdown:
    '<div class="btn-group"><button class="btn" onclick="Calendar.clCancel_ins(this);">NO</button><button class="btn"  onclick="Calendar.cnCancel_ins(this);">YES</button></div>',
  /*
		<div class="btn-group"><button class="btn" onclick="Calendar.clConfirm(this);">Cancel</button><button class="btn"  onclick="Calendar.cnConfirm(this);">Confirm</button></div>
	*/
  levelDropdown: function () {
    var select_out = document.createElement("select");
    select_out.setAttribute("class", "form-control select_student");
    select_out.setAttribute("name", "level_student");
    select_out.setAttribute("id", "level_student");
    select_out.style.width = "180px";
    select_out.style.padding = "0";
    var order_id = Object.keys(Calendar.config.student_level);
    order_id = order_id.sort();
    order_id.reduce(function (obj_temp, current) {
      var option = document.createElement("option");
      option.value = current;
      option.text = Calendar.config.student_level[current];
      obj_temp.add(option);
      return obj_temp;
    }, select_out);

    return select_out;
  },
  levelHtml: function (obj) {
    var select_out = Calendar.levelDropdown();

    div_ext = document.createElement("div");
    div_buttons = document.createElement("div");
    btn_cancel = document.createElement("button");
    btn_cancel.setAttribute("class", "btn");
    btn_cancel.setAttribute("onclick", "Calendar.clConfirm(" + obj + ")");
    btn_confirm = document.createElement("button");
    btn_confirm.setAttribute("class", "btn");
    btn_confirm.setAttribute("onclick", "Calendar.cnConfirm(" + obj + ")");
    btn_confirm.innerHTML = "Confirm";
    btn_cancel.innerHTML = "Cancel";

    div_buttons.appendChild(btn_cancel);
    div_buttons.appendChild(btn_confirm);
    div_ext.appendChild(select_out);
    div_ext.appendChild(div_buttons);

    return div_ext;
  },
  extra_level_html: function () {
    if (Object.keys(Calendar.config.extra_level).length > 0) {
      var select_type = document.createElement("select");
      var select_order = Object.keys(Calendar.config.extra_level);
      select_order = select_order.sort();
      select_type.className = "form-control select_extra";
      select_order.reduce(function (prev, current) {
        var option = document.createElement("option");
        option.value = current;
        option.text = Calendar.config.extra_level[current];
        prev.add(option);
        return prev;
      }, select_type);

      return select_type;
    } else {
      return "";
    }
  },
  general_events_after: function (id_event) {
    Calendar.makeDraggable_container("#e_" + id_event);

    Calendar.add_cancel_dialog("#e_" + id_event + " > .event_time > .cancel");
    Calendar.add_confirm_dialog("#e_" + id_event + " > .event_time > .confirm");
  },
  general_events_after_load_bulk: function () {
    Calendar.add_confirm_dialog(".confirm");
    Calendar.add_cancel_dialog(".cancel");
    Calendar.add_cancel_dialog_instructor(".remove_row");
    Calendar.add_cancel_dialog_special(".special_remove");
  },
  init: function (id_chain) {
    var number_rows = $(".day_header").length + 1;

    var pxwidth = number_rows * 200;
    $("div.cal_header").css("width", pxwidth - 200 + "px");
    $("div.cal_days").css("width", pxwidth - 200 + "px");
    $(".background_block").css("width", pxwidth + "px");
    $("#ddl_instructors").css("padding", "0");

    Calendar.load(id_chain);
    Calendar.load_wishlist(id_chain);

    if (number_rows <= 5) {
      $("#more_icon").remove();
    }

    $(".cal_half_hour").click(function () {
      Calendar.add(this);
    });

    $(".cal_body").scrollTop(60);

    $(".day_header > .title_row").click(function () {
      $(this).hide();
      $(this).siblings("span , input").hide();

      var prev = $(this).data("id");
      $("#ddl_instructors").val(prev);
      $(this).parent().append($("#ddl_instructors"));
      $("#ddl_instructors").focus();
    });

    $("#ddl_instructors").blur(function () {
      $(this).parent().find(".title_row").show();
      $(this).parent().find(".title_row").siblings("span , input").show();

      $("#hidden_box").append($("#ddl_instructors"));
    });
    $("#ddl_instructors").change(function () {
      var id_day = $(this).parent().data("id");
      var sel = $(this).val();

      var response = Calendar.update_instructor(id_day, sel);
      var title_div = $(this).parent().find(".title_row");
      title_div.data("id", response.instructor_id);

      name_app = response.instructor_name;
      name_app =
        name_app +
        (response.instructor_id == "" || response.instructor_id == null
          ? " " + $(this).parent().data("index")
          : "");
      if (response.message_error != "") {
        $("#message_error").html(response.message_error);
        $("#message_error").fadeIn();
      }

      title_div.siblings("input.sms_instructor").val(response.instructor_id);
      title_div.html(name_app);
      $(this).parent().find(".title_row").show();
      $(this).parent().find(".title_row").siblings("span , input").show();

      $("#hidden_box").append($("#ddl_instructors"));
    });

    $(".container_cal").css({ width: pxwidth + 220 - 60 });
    $("#more_icon").css({ display: "none" });
  },
  load: function (id_chain) {
    var url = base_url + "welcome/json_event";

    var params = { id_chain: id_chain };

    var ajax_init = $.ajax({
      url: url,
      type: "post",
      dataType: "json",
      data: params,
    });

    ajax_init.done(function (data) {
      if (data.rc != 0) {
        alert("Error loading calendar events, RC: " + data.rc);
      } else {
        $.each(data.events, function (i, event) {
          Calendar.buildEvent(event);
        });

        $(".cal_day").each(function () {
          $(this).droppable({
            accept: ".event",
            drop: function (event, ui) {
              Calendar.onDrop(event, ui, $(this));
            },
          });
        });

        $(".wishlist").droppable({
          accept: ".event",
          drop: function (event, ui) {
            if (ui.draggable.data().status != "3") {
              var element_selection = $(".cancel").filter(function () {
                return $(this).data("event") == ui.draggable.data("id");
              });
              Calendar.remove_booking(element_selection);
            } else {
              ui.draggable.animate(
                {
                  top: ui.draggable.data("o_top"),
                  left: 0,
                  height: ui.draggable.data("o_height"),
                },
                { duration: 600, easing: "easeOutBack" }
              );
            }
          },
        });
        $(".icon-comment").tooltip();

        Calendar.makeDraggable_container(".cal_day > .event");
        Calendar.add_confirm_dialog(".confirm");
        Calendar.add_cancel_dialog(".cancel");
        Calendar.add_cancel_dialog_instructor(".remove_row");
        Calendar.add_cancel_dialog_special(".special_remove");
        Calendar.add_redo_dialog(".undo");
      }
    });

    //$.getJSON(url, params, callback);
  },
  load_wishlist: function (id_chain) {
    var url = base_url + "welcome/json_event_wishlist";
    var params = { id_chain: id_chain, date_a: general_date };
    var callback = function (data) {
      if (data.rc != 0) {
        alert("Error loading calendar events, RC: " + data.rc);
      } else {
        $.each(data.events, function (i, event) {
          Calendar.buildEvent_wishlist(event);
        });

        Calendar.makeDraggable_wishlist("#wishlist_day > .event");
        Calendar.add_cancel_dialog_special("#wishlist_day .special_remove");
      }
    };

    $.ajax({
      url: url,
      type: "post",
      dataType: "json",
      data: params,
      success: callback,
      complete: function (data) {
        var data;
      },
    });
    //$.getJSON(url, params, callback);
  },
  typesDropdown: function () {
    var out = document.createElement("select");

    Object.keys(Calendar.config.booking_types).reduce(function (
      obj_temp,
      current
    ) {
      var option = document.createElement("option");
      option.value = current;
      option.text = Calendar.config.booking_types[current];
      obj_temp.add(option);
      return obj_temp;
    },
    out);
    $(out)
      .attr("class", "form-control select_student")
      .attr("id", "level_event")
      .attr("name", "level_event")
      .css({ width: "180px", padding: "0" });

    return out;
  },

  canvas_element_info: function (info_event) {
    //General Conditions [minutes,duration,obs,student_name,student_mobile,level,time_Description,hl,id,special,bk_level,current,bk_status,]

    var offset = parseInt(info_event.minutes, 10) / 15;
    var evtTop = offset * 20;
    var duration = parseInt(info_event.duration, 10) / 15;
    var evtHeight = duration * 20;

    var student_notes = info_event.obs != undefined ? info_event.obs : "";
    var student_name =
      info_event.student_name != undefined ? info_event.student_name : "";
    var student_mobile =
      info_event.student_mobile != undefined ? info_event.student_mobile : "";

    var ppal_class = "event";

    general = document.createElement("div");
    event_time = document.createElement("div");
    event_time.setAttribute("class", "event_time");

    event_level = document.createElement("div");
    event_level.setAttribute("class", "event_level");
    event_level.innerHTML = info_event.level;

    time = document.createElement("div");
    time.setAttribute("class", "time");
    time.innerHTML = info_event.time_description;

    n_cancel = document.createElement("div");
    n_cancel.setAttribute("class", "n_cancel");
    n_cancel.innerHTML =
      info_event.count_cancel !== undefined ? info_event.count_cancel : "";

    info_student = document.createElement("div");
    info_mobile = document.createElement("div");
    special_users = document.createElement("div");
    hours_left = document.createElement("div");
    notes = document.createElement("div");

    remove_final = document.createElement("span");
    check_remove = document.createElement("span");
    check_notes = document.createElement("span");
    check_confirm = document.createElement("span");
    check_remove = document.createElement("span");
    add_special = document.createElement("span");
    remove_final = document.createElement("span");

    undo_confirmed = document.createElement("span");

    check_sms = document.createElement("input");

    // Check notes
    //var remove_final = '<span class="icon-remove-circle icon-white remove" onclick="Calendar.remove_wishlist(this);"></span>';
    check_notes.setAttribute(
      "class",
      "icon-comment " + (student_notes == "" ? "icon-white" : "")
    );
    $(check_notes).data({
      toggle: "tooltip",
      placement: "top",
      "original-title": student_notes,
    });
    check_notes.setAttribute("onclick", "Calendar.show_notes(this,event)");

    hours_left.setAttribute("class", "hours_left icon-time");
    hours_left.style.color = info_event.hl < 0 ? "#FF0000" : "#FFFFFF";
    hours_left.innerHTML = info_event.hl;

    notes.setAttribute("class", "info_notes");
    notes.innerHTML = student_notes;

    check_sms.type = "checkbox";
    check_sms.setAttribute("class", "check_sms");
    check_sms.value = info_event.id;

    add_special.setAttribute("class", "icon-plus icon-white");
    add_special.setAttribute(
      "onclick",
      "Calendar.add_special(" + info_event.id + ");"
    );
    $(add_special).data("event", info_event.id);

    undo_confirmed.setAttribute("class", "icon-repeat icon-white undo");

    check_confirm.setAttribute("class", "icon-ok-circle icon-white confirm");
    $(check_confirm).data("event", info_event.id);

    check_remove.setAttribute("class", "icon-remove icon-white cancel");
    $(check_remove).data("event", info_event.id);

    remove_final.setAttribute("class", "icon-remove-circle icon-white remove");
    remove_final.setAttribute("onclick", "Calendar.remove_wishlist(this);");

    info_student.setAttribute("class", "info_student");
    info_student.innerHTML = student_name;

    info_mobile.setAttribute("class", "info_mobile");
    info_mobile.innerHTML = student_mobile;

    Object.keys(info_event.special).reduce(function (build_div, current) {
      var special_pack = document.createElement("div");
      var obj_info = info_event.special[current];
      var name =
        obj_info.name +
        " - " +
        obj_info.phone +
        (obj_info.number > 1 ? "(+ " + (obj_info.number - 1) + ")" : "");

      var description = document.createTextNode(name);
      var remove_div = document.createElement("div");
      remove_div.setAttribute(
        "class",
        "icon-remove-circle icon-white special_remove"
      );
      $(remove_div).data("event", info_event.id);
      $(remove_div).data("usr", obj_info.usr_id);
      special_pack.appendChild(description);
      special_pack.appendChild(remove_div);
      build_div.appendChild(special_pack);
      return build_div;
    }, special_users);
    ppal_class = ppal_class.concat(" lock20");

    if (info_event.bk_level == 1) {
      if (info_event.current == 0) {
        ppal_class = ppal_class.concat(" red_event");
      }
    } else {
      hours_left = "";
    }

    if (info_event.bk_level == 2) {
      ppal_class = ppal_class.concat(" not_available");
      check_sms = "";
    }

    if (info_event.bk_level != 3) {
      add_special = "";
    } else {
      special_users.className = "special_users";
    }

    if (info_event.status == 3) {
      ppal_class = ppal_class.concat(" confirmed");
    }

    event_time.appendChild(event_level);
    event_time.appendChild(time);
    if (hours_left != "") event_time.appendChild(hours_left); // Cond

    event_time.appendChild(n_cancel);

    //if(info_event.status != 3){

    event_time.appendChild(check_notes);
    if (check_sms != "") event_time.appendChild(check_sms);
    if (add_special != "") event_time.appendChild(add_special);

    event_time.appendChild(check_confirm);
    event_time.appendChild(check_remove);
    event_time.appendChild(remove_final);

    /*}else{
			if(check_sms!='')
				event_time.appendChild(check_sms);
		}*/

    event_time.appendChild(undo_confirmed);

    general.setAttribute("id", "e_" + info_event.id);
    general.setAttribute("class", ppal_class);
    general.style.top = evtTop + "px";
    general.style.height = evtHeight + "px";

    general.appendChild(event_time);
    general.appendChild(info_student);
    general.appendChild(info_mobile);
    general.appendChild(notes);
    general.appendChild(special_users);

    $(general).data({
      id: info_event.id,
      type: info_event.bk_level,
      minutes: info_event.minutes,
      duration: info_event.duration,
      group: info_event.group,
      student: info_event.student,
      o_top: evtTop,
      o_height: evtHeight,
      status: info_event.status,
    });

    return general;
  },
  /**
   * build the HTML event element. Store id, date
   * minutes offset from midnight and duration using jQuery.data()
   * on the event element
   */
  buildEvent: function (event) {
    var calDay = $("#day_" + event.group);
    calDay.append(Calendar.canvas_element_info(event));
  },
  buildEvent_wishlist: function (event) {
    //divide minutes offset from midnight by 30 to get cell offset;
    var calDay = $("#wishlist_day");
    calDay.append(Calendar.canvas_element_info(event));
  },

  /**
   * Enable event elements as draggable,
   * calendar day columns as droppable,
   * event_name click handler for in place editing
   * event name
   */

  onblur_save: function (obj) {
    var student = $(obj).parent().data("student");

    var new_value = $(obj).val();
    var params = { id_student: student, note: new_value, type: 1 };

    var e = $.ajax({
      url: base_url + "welcome/change_obs",
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        response = data;
      },
    });

    if (response.success == 1) {
      $("div:data(student)").each(function () {
        if ($(this).data("student") == student) {
          $(this).children(".info_notes").html(new_value);

          if (new_value != "") {
            $(this)
              .children(".event_time")
              .children(".icon-comment")
              .removeClass("icon-white");
          } else {
            $(this)
              .children(".event_time")
              .children(".icon-comment")
              .addClass("icon-white");
          }
        }
      });
    }

    $(obj).siblings(".info_notes").css("display", "block");
    $(obj).remove();
  },
  makeDraggable_wishlist: function (selector) {
    // Edit Notes

    $(selector).children(".info_notes").unbind("click");

    $(selector)
      .children(".info_notes")
      .click(function () {
        var content = $(this).html();
        var selec = Math.floor(Math.random() * 100 + 1);
        new_textarea =
          '<textarea onblur="Calendar.onblur_save(this);" id="editNotes_' +
          selec +
          '" class="edit_notes">' +
          content +
          "</textarea>";
        $(this).after(new_textarea);
        $(this).css("display", "none");
        $(this)
          .siblings("#editNotes_" + selec)
          .focus();
      });

    $(selector).draggable({
      //grid: [200, 20],
      helper: function (event) {
        var tt = $(this).clone();
        tt.removeClass("lock20");
        tt.data("come_wish", 1);

        height_temp = $(this).data("duration") * (20 / 15);
        tt.height(height_temp);

        $.each($(this).data(), function (name, value) {
          tt.data(name, value);
        });
        //$(tt).css("margin-left", event.clientX - $(event.target).offset().left);
        //$(tt).css("margin-top", event.clientY - $(event.target).offset().top);
        return tt;
      },

      handler: function () {
        return $(this).children(".event_time");
      },
      snap: ".cal_half_hour",
      snapMode: "inner",

      delay: 1000,
      opacity: 0.7,
      zIndex: 10,
    });
  },
  makeDraggable_container: function (selector) {
    $(selector).draggable({
      revert: function () {
        return false;
      },
      containment: ".container_cal",
      opacity: 0.7,
      start: function () {
        $(this).find(".event_name").unbind("click");
      },
      zIndex: 90,
      delay: 1000,
      handler: function () {
        return $(this).children(".event_time");
      },
      appendTo: "body",
      snap: ".cal_half_hour, .wishlist",
    });
    $(selector).resizable({
      handles: "s",
      grid: [0, 20],
      minHeight: 40,
      start: function () {
        $(this).find(".event_name").unbind("click");
      },
      stop: function (event, ui) {
        var parent = $(this).parent();
        Calendar.onResize(event, ui);
      },
    });
  },

  /**
   * Called on dropping event into calendar day column
   */
  onDrop: function (event, ui, parent) {
    var position = ui.position;

    var top = ui.position.top;
    if (ui.helper.data("come_wish") == 1) {
      top = top - 20;
    }

    var height = ui.draggable.height();

    var top_temp = top % 20;
    if (top_temp != 0) {
      if (top_temp > 10) {
        top = top + (20 - top_temp);
      } else {
        top = top - top_temp;
      }
    }

    var diff = height % 20;
    if (diff != 0) {
      if (diff > 10) {
        height = height + (20 - diff);
      } else {
        height = height - diff;
      }
    }

    var id = ui.draggable.data("id");
    var group = parent.data("id");
    var type = ui.helper.data("type");
    var student = ui.helper.data("student");
    if (ui.helper.data("come_wish") == 1) {
      var height = ui.helper.data("o_height");
    }
    /**
     * Calculate minutes offset from midnight
     * using top position (20px = 15 minutes),
     * calculate duration based on height of
     * event element
     */
    var minutes = (top / 20) * 15;
    var duration = (height / 20) * 15;
    //ui.draggable.find('.event_time').text('Saving..');
    this.save(id, type, group, minutes, duration, ui, student);
  },

  /**
   * Called on reszing an event element
   */
  onResize: function (event, ui) {
    var top = ui.position.top;
    var height = ui.helper.height();
    var student = ui.helper.data("student");
    var id = ui.helper.data("id");
    var group = ui.helper.data("group");
    /**
     * calculate minutes offset and duration -- see function above
     */

    var diff = height % 20;
    if (diff != 0) {
      if (diff > 10) {
        height = height + (20 - diff);
      } else {
        height = height - diff;
      }
    }

    var minutes = (top / 20) * 15;
    var duration = (height / 20) * 15;
    var type = ui.helper.data("type");

    this.save(id, type, group, minutes, duration, ui, student);
  },
  create_new_tab: function (minutes) {
    var input = $("select#level_event");
    var calDay = input.parent().parent().parent();
    var group = calDay.data("id");
    var type = $("#level_event").val();
    var student = $("#id_student").val();

    var name_student = $("#select_student_text").val();
    if (type == 1) {
      if (student == "" || student == null || name_student == "") {
        $("#select_student_text").css("border-color", "red");
        return false;
      }
    }

    if (type == 2) {
      var input_extra = $(input).siblings(".select_extra");
      if (input_extra.val() == "") {
        input_extra.css("border-color", "red");
        return false;
      }
      student = input_extra.val();
    }
    if (type == 3) {
      student = $("#level_student").val();
    }

    Calendar.save(null, type, group, minutes, 60, null, student);
    Calendar.config.new_flag = 0;
  },
  canvas_element_new: function (info) {
    var general = document.createElement("div");
    var event_time = document.createElement("div");
    var event_name = document.createElement("div");
    var select_name = document.createElement("input");
    var hidden_id = document.createElement("input");
    var select_level = Calendar.levelDropdown();
    var anchor_ok = document.createElement("a");
    var anchor_cancel = document.createElement("a");
    var select_extra = Calendar.extra_level_html();
    // classes and Ids

    $(general).addClass("event");
    $(event_time).addClass("event_time");
    $(event_name).addClass("event_name");

    select_name.setAttribute("type", "text");
    select_name.setAttribute("data-provide", "typeahead");
    select_name.setAttribute("autocomplete", "off");
    select_name.setAttribute("class", "name_search");
    select_name.setAttribute("placeholder", "Student");
    select_name.setAttribute("id", "select_student_text");

    hidden_id.setAttribute("type", "hidden");
    hidden_id.setAttribute("id", "id_student");
    hidden_id.setAttribute("name", "id_student");

    anchor_ok.setAttribute("class", "icon-thumbs-up");
    anchor_ok.setAttribute("href", "#");
    anchor_ok.setAttribute(
      "onclick",
      "javascript:Calendar.create_new_tab(" + info.minutes + ");"
    );

    anchor_cancel.setAttribute("class", "icon-thumbs-down");
    anchor_cancel.setAttribute("href", "#");
    anchor_cancel.setAttribute(
      "onclick",
      "javascript:Calendar.close_new_tab();"
    );

    // style
    general.style.top = info.top + "px";
    select_level.style.display = "none";
    //Group
    general.appendChild(event_time);
    general.appendChild(event_name);
    event_name.appendChild(select_level);
    event_name.appendChild(select_name);
    event_name.appendChild(hidden_id);

    if (select_extra != "") event_name.appendChild(select_extra);
    event_name.appendChild(Calendar.typesDropdown());
    event_name.appendChild(anchor_ok);
    event_name.appendChild(anchor_cancel);

    return general;
  },
  /**
   * Called on clicking a calendar cell,
   * add a blank event element with text input
   */
  close_new_tab: function () {
    var input = $("select#level_event");
    $("#hidden_box").prepend($("#select_student_text"));
    input.parent().parent().remove();
    Calendar.config.new_flag = 0;
  },

  add: function (cell) {
    if (Calendar.config.new_flag != 0) {
      return;
    } else {
      Calendar.config.new_flag = 1;
    }

    var evtTop = $(cell).position().top;
    var calDay = $(cell).parent();
    var group = calDay.data("id");

    /**
     * calculate minutes offset from midnight (20px = 15 minutes)
     */

    var top_temp = evtTop % 20;
    if (top_temp != 0) {
      if (top_temp > 10) {
        evtTop = evtTop + (20 - top_temp);
      } else {
        evtTop = evtTop - top_temp;
      }
    }

    var minutes = (evtTop / 20) * 15;

    info = { top: evtTop, minutes: minutes };
    calDay.append(Calendar.canvas_element_new(info));
    Calendar.update_typeahead();

    /**
     * focus on the name input and attach
     * event handlers for Return/Esc keystrokes
     */
    var input = calDay.find("select#level_event");

    input.change(function () {
      var val_selected = $(this).val();
      var style_e = "none",
        style_extra = "none",
        style_level = "none";
      if (val_selected == 1) {
        style_e = "block";
      }
      if (val_selected == 2) {
        style_extra = "block";
      }
      if (val_selected == 3) {
        style_level = "block";
      }
      $(this).siblings(".name_search").css("display", style_e);
      $(this).siblings(".select_extra").css("display", style_extra);
      $(this).siblings("#level_student").css("display", style_level);
    });

    $("#select_student_text").focus();
  },

  /**
   * Save the calendar event to the server
   */
  save: function (id, type, group, minutes, duration, ui, student) {
    var mem_group = "";
    var o_height = "";
    var o_top = "";
    var top = "";

    if (ui != null) {
      var come_t = ui.helper.data("come_wish");
      if (come_t == 1) {
        mem_group = group;
        o_height = ui.helper.data("o_height");
        o_top = ui.helper.data("o_top");
        top = ui.position.top;
      }
    }

    var url = base_url + "welcome/json_save_lesson";
    var params = {
      type: type,
      group: group,
      minutes: minutes,
      duration: duration,
      student: student,
    };
    // This is an update
    if (id != null) {
      params.id = id;
    }

    var info_s = "";

    $.ajax({
      url: url,
      type: "post",
      dataType: "json",
      data: params,
      success: function (data) {
        info_s = data;
      },
      complete: function (data) {
        var data;
      },
      async: false,
    });

    if (info_s != "") {
      var nameElt = null;
      var height = (duration * 20) / 15;
      if (id != null) {
        mem_group = ui.helper.data("group");
        o_height = ui.helper.data("o_height");
        o_top = ui.helper.data("o_top");
        top = ui.position.top;
      } else {
        mem_group = group;
      }

      if (info_s.rc != 0) {
        if (mem_group != group) {
          $("#day_" + mem_group).append(ui.helper);
        }

        if (id != null) {
          ui.helper.animate(
            { top: o_top, left: 0, height: o_height },
            { duration: 600, easing: "easeOutBack" }
          );
          $("#e_" + id)
            .find(".event_time")
            .children(".time")
            .html(info_s.time_description);
        } else {
          nameElt = $("#day_" + group)
            .find("select#level_event")
            .parent()
            .parent();
          nameElt.remove();
          $("#day_" + group)
            .find(".event")
            .remove();
          Calendar.load(group);
        }
      } else {
        if (id != null) {
          if (come_t != 1) {
            $("#day_" + group).append(ui.helper);
            ui.helper.css("left", "0");
          }

          if (come_t == 1) {
            $("#day_" + group).append($("#e_" + id));
            $("#e_" + id).css("top", (minutes * 20) / 15);
            $("#e_" + id).css("height", height);
            $("#e_" + id).draggable("destroy");
            $("#e_" + id).removeClass("red_event");
            $("#e_" + id).data("minutes", minutes);
            $("#e_" + id).data("duration", duration);
            $("#e_" + id).data("group", group);
            $("#e_" + id).data("o_top", (minutes * 20) / 15);
            $("#e_" + id).data("o_height", height);

            Calendar.makeDraggable_container("#e_" + id);
            Calendar.add_confirm_dialog(
              "#e_" + id + " > .event_time > .confirm"
            );
            Calendar.add_cancel_dialog("#e_" + id + " > .event_time > .cancel");
          } else {
            ui.helper.data("minutes", minutes);
            ui.helper.data("duration", duration);
            ui.helper.data("group", group);
            ui.helper.data("o_top", top);
            ui.helper.data("o_height", height);
          }
          $("#e_" + id)
            .find(".event_time")
            .children(".time")
            .html(info_s.time_description);

          //Tooltip??

          $("#e_" + id)
            .children(".event_time")
            .children(".icon-comment")
            .tooltip();
        } else {
          //[minutes,duration,obs,student_name,student_mobile,level,time_Description,hl,id,special,bk_level,current,bk_status,]
          var base_obj = {
            minutes: minutes,
            duration: duration,
            obs: "",
            student: info_s.type == 1 ? student : "",
            student_name: info_s.student_name,
            student_mobile: info_s.student_mobile,
            level: info_s.level_type,
            time_description: info_s.time_description,
            hl: info_s.hl,
            id: info_s.id,
            special: [],
            bk_level: info_s.type,
            current: "1",
            status: 0,
            group: group,
          };
          $("#day_" + group)
            .find("select#level_event")
            .parent()
            .parent()
            .remove();
          $("#day_" + group).append(Calendar.canvas_element_info(base_obj));
          Calendar.general_events_after(info_s.id);
        }
        if (type == 1) {
          $("div:data(student)").each(function () {
            if ($(this).data("student") == student) {
              $(this)
                .children(".event_time")
                .find(".hours_left")
                .html(info_s.hl);
              if (info_s.hl < 0) {
                $(this)
                  .children(".event_time")
                  .find(".hours_left")
                  .css("color", "red");
              }
              if ($(this).parent().attr("class") == "wishlist") {
                $(this).remove();
              }
            }
          });
        }
      }
    }
  },

  remove_booking: function (obj) {
    var data_gen = $(obj).parent().parent().data();
    var id_booking = data_gen.id;
    event = $("#e_" + id_booking);
    //event.css({'top':'0px','left':'0px','position':''});
    // top: 20px; height: 160px; opacity: 1; z-index: 2; left: 0px; position: absolute;

    var cssObject = event.prop("style");
    cssObject.removeProperty("top");
    cssObject.removeProperty("left");
    cssObject.removeProperty("position");

    var response = Calendar.change_status(id_booking, 1);

    if (response.status == 1) {
      event.attr("style", "top:0px;");
      event.addClass("lock20");
      event.draggable("destroy");
      if (data_gen.type == 1 || data_gen.type == 3) {
        if (data_gen.type == 1) {
          $(".wishlist > div:data(student)").each(function () {
            if ($(this).data("student") == response.student) {
              $(this).remove();
            }
          });
        }
        event
          .children(".event_time")
          .children(".n_cancel")
          .html(response.cancel_count);
        Calendar.makeDraggable_wishlist(event);
        $("#wishlist_day").prepend(event);
        if (data_gen.type == 1) {
          $("div:data(student)").each(function () {
            if ($(this).data("student") == response.student) {
              $(this)
                .children(".event_time")
                .find(".hours_left")
                .html(response.hl);
              if (response.hl < 0) {
                $(this)
                  .children(".event_time")
                  .find(".hours_left")
                  .css("color", "red");
              } else {
                $(this)
                  .children(".event_time")
                  .find(".hours_left")
                  .css("color", "white");
              }
            }
          });
        }
      } else {
        event.remove();
      }
    }

    //var response = Calendar.change_status()
  },

  change_status: function (id, new_status) {
    var new_status;
    var params = {
      id_booking: id,
      status: new_status,
    };
    var ejem = $.ajax({
      url: base_url + "welcome/change_status",
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        new_status = data;
      },
    });

    return new_status;
  },

  /**
   * Delete the event on the server
   */
  remove: function (elt) {
    var evtElt = $(elt).parent();
    var id = evtElt.data("id");
    var url = "/index.php?cmd=event";
    var params = { id: id, d: "Y" };
    var callback = function (data) {
      if (data.rc != 0) {
        alert("Error removing event from calendar, RC: " + data.rc);
      } else {
        //hide the event element using the 'fade' effect
        $("#e_" + id).fadeOut(800, function () {
          $(this).remove();
        });
      }
    };
    $.post(url, params, callback, "json");
  },

  update_typeahead: function () {
    $(".name_search").typeahead({
      source: function (query, process) {
        $("#id_student").val("");
        $.ajax({
          url: base_url + "welcome/search_student",
          type: "post",
          dataType: "json",
          data: { search: query },
          success: function (data) {
            objects = [];
            map = {};
            $.each(data.options, function (i, object) {
              map[object.name] = object;
              objects.push(object.name);
            });

            process(objects);
          },
        });
      },
      items: 12,
      updater: function (item) {
        $("#id_student").val(map[item].id);
        return item;
      },
    });
  },

  update_typeahead_special: function () {
    $(".name_search_special").typeahead({
      source: function (query, process) {
        $("#id_student").val("");
        $.ajax({
          url: base_url + "welcome/search_student_special",
          type: "post",
          dataType: "json",
          data: { search: query },
          success: function (data) {
            objects = [];
            map = {};
            $.each(data.options, function (i, object) {
              map[object.name] = object;
              objects.push(object.name);
            });

            process(objects);
          },
        });
      },
      items: 12,
      updater: function (item) {
        $("#id_student_special").val(map[item].id);
        return item;
      },
    });
  },
  create_html_user: function () {},
  update_instructor: function (day, instructor) {
    var params = { bk_day: day, instructor: instructor };

    var response;

    var e = $.ajax({
      url: base_url + "welcome/change_instructor",
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        response = data;
      },
    });

    return response;
  },
  send_sms_ind: function (type, selector_jq) {
    // 1 Confirm
    // 2 Cancel

    var url = base_url;
    if (type == 1) {
      url += "welcome/send_confirm_sms";
    }
    if (type == 2) {
      url += "welcome/send_cancel_sms";
    }
    if (type == 3) {
      url += "welcome/send_confirm_sms";
    }
    var checked_vector = "";

    $(".check_sms:checked").each(function () {
      if (checked_vector == "") {
        checked_vector = $(this).val();
      } else {
        checked_vector += "," + $(this).val();
      }
      if (type == 2) {
        Calendar.remove_booking($(this));
      }
    });

    if (checked_vector == "") {
      alert("You have to check the students or lessons to SMS");
      return;
    }

    var response;

    var params = { v_days: checked_vector };
    if (type == 3) {
      message = selector_jq.val();
      if (message == "") {
        selector.css("border", "3px solid red");
        return;
      } else {
        params["message"] = message;
      }
    }
    var e = $.ajax({
      url: url,
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        response = data;
      },
    });

    $(".check_sms").prop("checked", false);
    Calendar.alert_obj(response);
  },
  send_sms_confirm_day_after: function (day) {
    var response;

    url = base_url + "welcome/send_confirmation_all";

    var params = { date: day };

    var e = $.ajax({
      url: url,
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        response = data;
      },
    });
    $(".cal_days").children().find(".check_sms").prop("checked", false);
    Calendar.alert_obj(response);
  },
  send_sms_confirm_instructors: function (day, selector) {
    var response;

    var check_ins = $(selector)
      .filter(function () {
        return this.checked;
      })
      .map(function () {
        return this.value;
      })
      .get();

    if (check_ins.length == 0) {
      alert("You have to check the instructors to send a SMS");
      return false;
    }
    var check_for = check_ins.join();

    url = base_url + "welcome/send_confirmation_instructors";

    var params = { date: day, instructors: check_for };

    var e = $.ajax({
      url: url,
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        response = data;
      },
    });
    $(selector).attr("checked", false);
    //$('.cal_days').children().find('.check_sms').prop('checked',false);
    Calendar.alert_obj(response);
  },
  alert_obj: function (obj_array) {
    msg = "SMS \n";
    new_div =
      '<div class="alert alert-block"><table class="table"><tr><td>Name</td><td>Mobile</td><td>Status</td></tr>';

    $.each(obj_array, function (index, value) {
      stats = "Error";
      if (value.sh_status == 1) {
        stats = "Send";
      }
      new_div +=
        "<tr><td>" +
        value.name +
        "</td><td>" +
        value.sh_mobile +
        "</td><td>" +
        stats +
        "</td></tr>";
    });
    new_div += "</table></div>";

    $("#message_q").html(new_div);
    $("#message_q").css("display", "block");
  },
  add_confirm_dialog: function (selector) {
    $(selector).popover({
      html: true,
      title: function () {
        return '<div style="color:black">New level</div>';
      },
      content: function () {
        return Calendar.levelHtml($(this).data("event"));
      },
      placement: "bottom",
      container: "body",
    });
  },
  add_cancel_dialog: function (selector) {
    $(selector).popover({
      html: true,
      title: function () {
        var origin =
          '<div style="color:black">Are you sure you would like to cancel this session?</div>';
        return origin;
      },
      content: function () {
        return Calendar.cancelHTML.replace(
          new RegExp("this", "g"),
          $(this).data("event")
        );
      },
      placement: "bottom",
      container: "body",
    });
  },
  add_cancel_dialog_instructor: function (selector, message) {
    $(selector).popover({
      html: true,
      title: function () {
        var origin =
          '<div style="color:black">Are you sure you would like to delete this column? all the lessons will be deleted !</div>';
        return origin;
      },
      content: function () {
        return Calendar.cancelHTMLinstructor.replace(
          new RegExp("this", "g"),
          $(this).data("code")
        );
      },
      placement: "bottom",
      container: "body",
    });
  },
  add_cancel_dialog_special: function (selector) {
    $(selector).popover({
      html: true,
      title: function () {
        var origin =
          '<div style="color:black">Are you sure you would like to delete this user?</div>';
        return origin;
      },
      content: function () {
        return Calendar.cancelHTMLspecial.replace(
          new RegExp("this", "g"),
          $(this).data("event") + "," + $(this).data("usr")
        );
      },
      placement: "bottom",
      container: "body",
    });
    $(selector).on("show.bs.popover", function () {
      return $(this).parents("#wishlist_day").length == 0;
    });
  },
  add_redo_dialog: function (selector) {
    $(selector).popover({
      html: true,
      title: function () {
        var origin =
          '<div style="color:black">You re about to change the status of this lesson to available again, are you sure?<br/>(The system wont modify the students current level)</div>';
        return origin;
      },
      content: function () {
        return Calendar.redoHTML.replace(
          new RegExp("this", "g"),
          $(this).parent().parent().data("id")
        );
      },
      placement: "bottom",
      container: "body",
    });
  },
  clCancel: function (code) {
    $(".cancel")
      .filter(function (element) {
        return $(this).data("event") == code;
      })
      .popover("hide");

    //$(".cancel[data-event='"+code+"']").popover('hide');
  },
  cnCancel: function (code) {
    group = $(".cancel").filter(function (element) {
      return $(this).data("event") == code;
    });
    group.popover("hide");
    Calendar.remove_booking(group);
  },
  clCancel_ins: function (code) {
    group = $(".remove_row").filter(function (element) {
      return $(this).data("code") == code;
    });
    $(group).popover("hide");
  },
  cnCancel_ins: function (code) {
    //Calendar.remove_booking($(".cancel[data-event='"+code+"']"));
    //$(".remove_row[data-event='"+code+"']").popover('hide');

    $("#add_ins").val("");
    $("#del_ins").val("1");
    $("#col_id").val(code);
    $("#add_column").submit();
  },
  clConfirm: function (obj) {
    var group = $(".confirm").filter(function () {
      return $(this).data("event") == obj;
    });

    group.popover("hide");
  },
  cnConfirm: function (obj) {
    var popop = $(".confirm").filter(function () {
      return $(this).data("event") == obj;
    });
    var new_level = $("body").children().find("#level_student").val();

    var params = { id_booking: obj, status: 3, new_level: new_level };
    var response;
    var re = $.ajax({
      url: base_url + "welcome/change_status",
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        response = data;
      },
    });

    var pop_event_time = popop.parent();
    var pop_event = popop.parent().parent();

    popop.popover("hide");

    if (response.status == 3) {
      //pop_event_time.children('span').remove();
      pop_event.addClass("confirmed");
      pop_event.data("status", "3");
      Calendar.add_redo_dialog(".undo");
    }
  },
  show_notes: function (obj, event) {
    var content = $(obj).parent().parent().children(".info_notes").html();
    var offset = $(obj).offset();
    real_x = 300;
    real_y = 250;
    var student = $(obj).parent().parent().data("student");
    new_textarea =
      '<div class="div_new"  style="top:' +
      real_y +
      "px;left:" +
      real_x +
      'px" ><textarea id="editNotes"class="edit_notes">' +
      content +
      "</textarea><div>";
    $(".background_block").css({
      width: $(".calendar").width(),
      height: $(".calendar").height(),
    });
    $(".background_block").fadeIn();
    $(".cal_body").append(new_textarea);

    $("#editNotes").unbind("blur");
    $("#editNotes").blur(function () {
      var type = $(obj).parent().parent().data("type");
      var id_event = $(obj).parent().parent().data("id");
      var new_value = $(this).val();
      var params = {
        id_student: student,
        note: new_value,
        type: type,
        id_event: id_event,
      };

      var e = $.ajax({
        url: base_url + "welcome/change_obs",
        type: "post",
        dataType: "json",
        data: params,
        async: false,
        success: function (data) {
          response = data;
        },
      });

      if (response.success == 1) {
        if (type == 1) {
          $("div:data(student)").each(function () {
            if ($(this).data("student") == student) {
              $(this).children(".info_notes").html(new_value);
              if (new_value != "") {
                $(this)
                  .children(".event_time")
                  .children(".icon-comment")
                  .removeClass("icon-white");
              } else {
                $(this)
                  .children(".event_time")
                  .children(".icon-comment")
                  .addClass("icon-white");
              }
              $(this)
                .children(".event_time")
                .children(".icon-comment")
                .attr("data-original-title", new_value);
              $(this)
                .children(".event_time")
                .children(".icon-comment")
                .tooltip();
            }
          });
        } else {
          $(obj).parent().parent().children(".info_notes").html(new_value);
          if (new_value != "") {
            $(obj).parent().children(".icon-comment").removeClass("icon-white");
          } else {
            $(obj).parent().children(".icon-comment").addClass("icon-white");
          }
        }
      }

      $(".background_block").fadeOut();
      $(this).remove();
    });
    $("#editNotes").focus();
  },
  add_special: function (obj) {
    if (Calendar.config.new_flag != 0) {
      return;
    } else {
      Calendar.config.new_flag = 1;
    }
    var special_users = $("#e_" + obj).children(".special_users");

    typeahead =
      '<input type="text" data-provide="typeahead" autocomplete="off" class="name_search_special" placeholder="Student" id="select_student_text" />';
    typeahead +=
      '<a class="icon-ok correicon"  href="#" onclick="Calendar.special_plus(this)"  ></a>' +
      '<a class="icon-remove correicon" href="#" onclick="Calendar.special_exit(this)"></a>' +
      '<input id="id_student_special" type="hidden" />';
    special_users.after(typeahead);
    special_users.fadeOut();
    Calendar.update_typeahead_special();
    $("#select_student_text").focus();
  },
  remove_wishlist: function (obj) {
    if (confirm("Do you want to remove this item from the wishlist?")) {
      var booking = $(obj).parent().parent().data("id");
      var params = { booking: booking };

      var e = $.ajax({
        url: base_url + "welcome/remove_wishlist",
        type: "post",
        dataType: "json",
        data: params,
        async: false,
        success: function (data) {
          response = data;
        },
      });

      if (response.success == 1) {
        $(obj).parent().parent().remove();
      }
    }
  },
  special_exit: function (obj) {
    var special_users = $(obj).parent().children(".special_users");
    special_users.fadeIn();
    $(".name_search_special").remove();
    $(".correicon").remove();
    $("#id_student_special").remove();
    Calendar.config.new_flag = 0;
  },
  special_plus: function (obj) {
    var group = $(obj).parent().data("id");
    var student = $("#id_student_special").val();
    if (student == "") {
      $("#select_student_text").css("border", "1px solid red");
      return;
    }
    var params = { group: group, student: student };
    var response;
    var re = $.ajax({
      url: base_url + "welcome/save_special_lesson",
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        response = data;
      },
    });

    var special_users = $(obj).parent().children(".special_users");
    special_users.fadeIn();

    if (response.warning == "") {
      //var new_icon = '<a href="#" class="icon-user" data-toggle="tooltip" title="'+response.name+'">'+response.hours_left+'</a>';
      var name = response.name + " - " + response.phone;
      if (response.number > 1) {
        name = name + "(+ " + (response.number - 1) + ")";
      }
      var special_number = Math.floor(Math.random() * 100 + 1);
      var new_icon =
        "<div>" +
        name +
        '<div class="icon-remove-circle icon-white special_remove rr_' +
        special_number +
        '"   data-event="' +
        $(obj).parent().data().id +
        '" data-usr="' +
        response.usr_id +
        '"></div></div>';
      special_users.append(new_icon);
      Calendar.add_cancel_dialog_special(".rr_" + special_number);
      // Update lesson
      $(obj)
        .parent()
        .children()
        .find(".event_level")
        .html(response.level_updated.desc);
    } else {
      $("#message_error").html(response.warning);
      $("#message_error").css("display", "block");
    }
    $(".name_search_special").remove();
    $(".correicon").remove();
    $("#id_student_special").remove();
    Calendar.config.new_flag = 0;
  },
  remove_special: function (group, student, obj) {
    var params = { group: group, student: student };
    var response;
    var re = $.ajax({
      url: base_url + "welcome/remove_special",
      type: "post",
      dataType: "json",
      data: params,
      async: false,
      success: function (data) {
        response = data;
      },
    });

    if (response.success == 1) {
      $(obj)
        .parents(".event")
        .children()
        .find(".event_level")
        .html(response.level_updated.desc);
      $(obj).parent().remove();
    } else {
      $("#message_error").html(response.warning);
      $("#message_error").css("display", "block");
    }
  },
  clCancel_special: function (event, usr) {
    $(".special_remove")
      .filter(function () {
        return $(this).data("usr") == usr && $(this).data("event") == event;
      })
      .popover("hide");
  },
  cnCancel_special: function (event, usr) {
    var obj = $(".special_remove").filter(function () {
      return $(this).data("usr") == usr && $(this).data("event") == event;
    });
    obj.popover("hide");
    Calendar.remove_special(event, usr, obj);
  },
  cancel_redo: function (obj) {
    var redo_button = $("#e_" + obj)
      .children()
      .find(".undo");
    redo_button.popover("hide");

    return false;
  },
  accept_redo: function (obj) {
    var redo_button = $("#e_" + obj);
    var status = Calendar.change_status(obj, 0);
    if (status.status == "0") {
      redo_button.removeClass("confirmed");
    }
    $("#e_" + obj)
      .children()
      .find(".undo")
      .popover("hide");
  },
};
