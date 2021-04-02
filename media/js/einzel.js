
  // Funktion zum berechnen der Summe 
  (function( $ ){ 
        $.fn.sum=function () {
            let sum=0;
            $(this).each(function(index, element){
                if($(element).val()!="")
                    sum += parseFloat($(element).val());
            });
        return sum;
        }; 
    })( jQuery );

	// Falls keine Nummer oder Wert dann 0
	function check(x) { 
		if($.isNumeric(x) && x >= 0) {
			return x;
		}
		else {
			return 0;
		}
	};

    function loadData() { 
        let routestotal =  $( "#soll_value input" ).sum();    // Hole die Routenanzahl Total
        $('#total').text(routestotal);
		let total_line = $('#total_lines ').text();			// Hole die Gesamtzahl der Linien 
		let density = (parseFloat(routestotal/total_line).toFixed(1)); // Routendichte berechnen
		$('#density').text(check(density));				   // Routendichte eintragen

		let g10  = check($('#routesoll10').val());  // grade10 = 3
		let g11  = check($('#routesoll11').val());
		let g12  = check($('#routesoll12').val());
		let g13  = check($('#routesoll13').val());
		let g14  = check($('#routesoll14').val());
		let g15  = check($('#routesoll15').val());
		let g16  = check($('#routesoll16').val());
		let g17  = check($('#routesoll17').val());
		let g18  = check($('#routesoll18').val());
		let g19  = check($('#routesoll19').val()); 
		let g20  = check($('#routesoll20').val());
		let g21  = check($('#routesoll21').val());
		let g22  = check($('#routesoll22').val());
		let g23  = check($('#routesoll23').val());
		let g24  = check($('#routesoll24').val());
		let g25  = check($('#routesoll25').val());
		let g26  = check($('#routesoll26').val());
		let g27  = check($('#routesoll27').val());
		let g28  = check($('#routesoll28').val());
		let g29  = check($('#routesoll29').val());
		let g30  = check($('#routesoll30').val());
		let g31  = check($('#routesoll31').val());
		let g32  = check($('#routesoll32').val());
		let g33  = check($('#routesoll33').val());
		let g34  = check($('#routesoll34').val());
		let g35  = check($('#routesoll35').val());
		let g36  = check($('#routesoll36').val());

		// Erstelle ein Object mit den Werten Grad und Routenanzahl
		let obj_routes = {g10,g11,g12,g13,g14,g15,g16,g17,g18,g19,g20,g21,g22,g23,g24,g25,g26,g27,g28,g29,g30,g31,g32,g33,g34,g35,g36};
		let json_routes = JSON.stringify(obj_routes);	// Konvertierung  JS object to JSON string
		$('#routessoll_ind').val(json_routes);  	   // Werte als Value des Hidden Fields routessoll_ind verwenden
	};

    // Beim ersten Laden Daten berechnen
    $(document).ready(function () {
       updateData();
       loadData();
    });
    // Nach einer Änderung Daten neu berechnen
     $('#gradetable').change(function() {    
        updateData();
        loadData();
    });

    // Defaults für Charts
    Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
        align: 'end',
        anchor: 'end',
        color: '#555',
        offset: 0,
        font: {
            size: 16,
            weight: 'bold'
        },
    });

    var config = {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [],
            }]
        },
        // Abstand von Legend nach unten 3.Grade ...
        plugins: [{
            beforeInit: function(chart, options) {
             chart.legend.afterFit = function() {this.height = this.height + 20;};
            }
        }],
        options: {
            legend: { display: false },
            animation: {duration: 0 },
            hover: { animationDuration: 0 },
            responsiveAnimationDuration: 0 ,
            scales: {
                yAxes: [{
                    ticks: {display: false}
                }]
                
            }
        }
    };

    var ctx = document.getElementById("myChart").getContext("2d");
    var myChart = new Chart(ctx, config);

     // Funktion zum berechnen der Summe der einzelnen Grade
     (function( $ ){ 
        $.fn.sum=function () {
            let sum=0;
            $(this).each(function(index, element){
                if($(element).val()!="")
                    sum += parseFloat($(element).val());
            });
            return sum;
        }; 
    })( jQuery )

    // Die Daten (Angabe Summe der einzelnen Grade) muss nach der Eingabe neu erstellt werden
    // Nur diese Daten werden dann als Update in das Chart eingespielt
    function updateData(){

        // Erstelle ein Array aus den Readonly-Fields 
        let arrayRoutes = [];    
        $( "#soll_value input" ).each(function( index ) {
            arrayRoutes.push($( this ).val());
        });

        // Erstelle das Array für die Label
        let arrayLabel = [];    
        $( "#gradelabel label" ).each(function( index ) {
            arrayLabel.push($(this).text().trim()); // Trim Whitespace & \n
        });

        // Erstelle das Array für die Farben
        let arrayColor = [];    
        $( "#gradelabel label" ).each(function( index ) {
            arrayColor.push($( this ).css("border-top-color"));
        });

        let dataObj =  arrayRoutes;
        let newData=[];
        newData = dataObj;
        newData.push()
        myChart.data.labels = arrayLabel,                       // Update Label
        myChart.data.datasets[0].data = newData;				// Update Data Routes
        myChart.data.datasets[0].backgroundColor = arrayColor;  // Update BackgroundColors
        myChart.update();
    };
