let current_modal = null;

function onTwoDigits(n) {
    return n > 9 ? "" + n: "0" + n;
}

function dayIntToString(day_int) {
    switch (day_int) {
        case 0:
            return "Lundi";
        case 1:
            return "Mardi";
        case 2:
            return "Mercredi";
        case 3:
            return "Jeudi";
        case 4:
            return "Vendredi";
        case 5:
            return "Samedi";
        default:
            return "Dimanche";
    }
}

function openRDVDialog(event) {
    let dialog = document.getElementById('RDV-confirm');
    let elt_doctor = document.getElementById('RDV-confirm-doctor');
    let elt_date = document.getElementById('RDV-confirm-date');
    let elt_time = document.getElementById('RDV-confirm-time');
    let elt_place = document.getElementById('RDV-confirm-place');
    let elt_slot = document.getElementById('RDV-confirm-slot-id');

    elt_doctor.innerText = document.getElementById('timetable').dataset.doctor;
    elt_date.innerText = event.target.dataset.day + " " + event.target.dataset.date;
    elt_time.innerText = "De " + event.target.dataset.start + " à " + event.target.dataset.end;
    elt_place.innerText = event.target.dataset.place;
    elt_slot.value = event.target.dataset.slot_id;

    let modal = new bootstrap.Modal(dialog);
    modal.show();
}

function closeModal() {
    current_modal.hide();
}

function createSlotButton(place, start, end, day, date, number_slots, slot_height, slot_id) {
    let button = document.createElement("button");
    button.classList.add("slot-btn");
    button.classList.add("btn");
    button.classList.add("btn-secondary");
    button.style.height = slot_height*number_slots + "px";
    
    let h_start = onTwoDigits(Math.floor(start/3600));
    let h_end = onTwoDigits(Math.floor(end/3600));
    let min_start = onTwoDigits(Math.floor((start%3600)/60));
    let min_end = onTwoDigits(Math.floor((end%3600)/60));

    button.innerText = place;

    button.dataset.start = h_start + ":" + min_start;
    button.dataset.end = h_end + ":" + min_end;
    button.dataset.place = place;
    button.dataset.day = day;
    button.dataset.date = date;
    button.dataset.slot_id = slot_id;

    button.addEventListener("click", openRDVDialog);

    return button;
}

function insertSlot(day, date, position_start, position_end, time_start, time_end, place, slot_height, slot_id) {
    // If day is out of week, dont place it
    if (day > 7) {
        return;
    }
    
    // Expand corresponding <td>
    let td_start = document.getElementById("table-"+day+"-"+position_start);
    td_start.rowSpan = position_end - position_start;
    td_start.appendChild(createSlotButton(place, time_start, time_end, dayIntToString(day), date, position_end-position_start, slot_height, slot_id));
    
    for (i=position_start+1; i<position_end; i++) {
        // remove <td>
        document.getElementById("table-"+day+"-"+i).remove();
    }
}