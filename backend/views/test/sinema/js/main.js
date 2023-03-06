// Selections

const rows = document.querySelectorAll('.rows')
const cost = document.querySelector('.cost')

//Select seats total

const seatsTotal = document.querySelector('.seatsTotal')
const places = document.querySelector('.places')

//Select button

const buy = document.querySelector('.btnBuy')
const reset = document.querySelector('.btnReset')

//Select film selector

let film = document.getElementById('film')

// Select a seat

const seat = document.querySelectorAll('.seat:not(.occupied)')

//Global variables

let removeSeat = false;
let seatRow = 0;
let setNumber = 0;

//Select rows number and set innerHTML value

let rowNumb = document.querySelectorAll('.rowNumb')
rowNumb.forEach(element => {
    element.innerHTML = `Row ${element.id}`
})

/* UPDATE VALUES FUNCTION */

const updateValues = (seatNumber, seatRow, removeSeat) => {
    //Select seats with class 'selected'
    let seatSelected = document.querySelectorAll('.row .seat.selected')

    //Create array for localstorage
    let localStorageSeats = [...seatSelected].map(chair => {
        return [...seat].indexOf(chair)
    })

    //Save seats selected inside browser localstorage
    localStorage.setItem('armchairs', JSON.stringify(localStorageSeats) )

    //Populate info area
    if (seatNumber && seatRow !== undefined){
        if (!removeSeat){
            places.innerHTML += ` ${seatNumber}/${seatRow} `
            //Save value inside browser localstorage
            localStorage.setItem('S&&F', places.innerHTML)
        } else {
            places.innerHTML = places.innerHTML.replace(` ${seatNumber}/${seatRow} -`,"")
            //Save value inside browser localstorage
            localStorage.setItem('S&&F', places.innerHTML)
        }
    }

    //Set ticket price
    let ticket = film.value;
    //Seats total number
    seatsTotal.innerHTML = seatSelected.length;
    //price
    cost.innerHTML = seatSelected.length * ticket;
}
/* Load data from browser localstorage */

let seatNotSelected = document.querySelectorAll('.seat:not(.selected)');

const loadData = () => {
    let armchairs = JSON.parse(localStorage.getItem('armchairs'))
    let movie = localStorage.getItem('movie');
    let price = localStorage.getItem('price');
    let occupate = JSON.parse(localStorage.getItem('occupate'));

    //Set selected seats
    if (armchairs !== null && armchairs.length > 0){
        seat.forEach((armchair, index) => {
            if (armchairs.indexOf(index) > -1){
                armchair.classList.add('selected')
            }
        })
    }

    //Set occupied seats
    if (occupate !== null && occupate.length > 0){
        seatNotSelected.forEach((armchair, index) => {
            if (occupate.indexOf(index) > -1){
                armchair.classList.add('occupied')
            }
        })
    }

    //Set movie title
    let movieSavedIdx = localStorage.getItem('movie');
    if (movieSavedIdx !== null){
        film.selectedIndex = movieSavedIdx;
    }

    updateValues();

    //Populate area info
    let seatsInfo = localStorage.getItem('S&&F');
    places.innerHTML = seatsInfo;
}

loadData();


/* Select seat and add event listener */

const seatReload = document.querySelectorAll('.seat:not(.occupied)')
seatReload.forEach(element => {
    // Set seat number
    element.innerHTML = element.id;
    // Add event listener
    element.addEventListener('click', () => {
        seatRow = element.parentElement.id;
        seatNumber = element.id;

        //Add and remove color class
        if (element.classList.value == 'seat'){
            element.classList.add('selected');
            //Set false remove variable
            removeSeat = false;
            //Update values
            updateValues(seatNumber, seatRow, removeSeat)
        } else {
            element.classList.remove('selected');
            //Set true remove variable
            removeSeat = true;
            updateValues(seatNumber, seatRow, removeSeat)
        }

    })
})

/* Movie title event listener */

film.addEventListener('change', e => {
    //Ticket
    let ticket = parseInt(e.target.value);
    let movieTitle = e.target.selectedIndex;
    // Save inside localstorage movie title
    localStorage.setItem('movie', movieTitle);
    //Save price
    localStorage.setItem('price', ticket);
    //Update values
    updateValues();
})

/* Button buy event listener */
buy.addEventListener('click', () => {
    let seat = document.querySelectorAll('.seat.selected');
    seat.forEach(element => {
        element.classList.remove('selected');
        element.classList.add('occupied');
        //Clear all fields
        element.innerHTML = '';
        seatsTotal.innerHTML = '';
        cost.innerHTML = '';
        places.innerHTML = '';
        //Clear localstorage
        localStorage.clear();

        //Save occupied seats inside the localstorage
        let seatBusySelect = document.querySelectorAll('.row .seat.occupied');
        const localStorageSeatsOccupied = [...seatBusySelect].map(seat => {
            return [...seatNotSelected].indexOf(seat)
        })
        localStorage.setItem('occupate',JSON.stringify(localStorageSeatsOccupied) );
    })
})

/*Button reset event listener */
reset.addEventListener('click', () => {


    const seatOccupied = document.querySelectorAll('.seat.occupied')
    let seatSelected = document.querySelectorAll('.row .seat.selected')
    seatOccupied.forEach( element => {
        element.classList.remove('occupied')
    })
    seatSelected.forEach( element => {
        element.classList.remove('selected')
    })
    //Clear all fields
    seatsTotal.innerHTML = '';
    cost.innerHTML = '';
    places.innerHTML = '';
    //Clear localstorage
    localStorage.removeItem('armchairs');
    localStorage.removeItem('occupate');
    localStorage.clear();
    /*Refresh page*/
    location.reload();
    return false;
})