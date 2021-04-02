
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

		let route10  = check($('#routesoll10').val());  
		let route11  = check($('#routesoll11').val());
		let route12  = check($('#routesoll12').val());
		let route13  = check($('#routesoll13').val());
		let route14  = check($('#routesoll14').val());
		let route15  = check($('#routesoll15').val());
		let route16  = check($('#routesoll16').val());
		let route17  = check($('#routesoll17').val());
		let route18  = check($('#routesoll18').val());
		let route19  = check($('#routesoll19').val());
		let route20  = check($('#routesoll20').val());
		let route21  = check($('#routesoll21').val());
		let route22  = check($('#routesoll22').val());
		let route23  = check($('#routesoll23').val());
		let route24  = check($('#routesoll24').val());
		let route25  = check($('#routesoll25').val());
		let route26  = check($('#routesoll26').val());
		let route27  = check($('#routesoll27').val());
		let route28  = check($('#routesoll28').val());
		let route29  = check($('#routesoll29').val());
		let route30  = check($('#routesoll30').val());
		let route31  = check($('#routesoll31').val());
		let route32  = check($('#routesoll32').val());
		let route33  = check($('#routesoll33').val());
		let route34  = check($('#routesoll34').val());
		let route35  = check($('#routesoll35').val());
		let route36  = check($('#routesoll36').val());

		// Erstelle ein Object mit den Werten Grad und Routenanzahl
		let obj_routes = {'g10':route10, 'g11':route11,'g12':route12,'g13':route13, 'g14':route14, 'g15': route15,'g16': route16,'g17': route17,'g18': route18,'g19':route19,
                          'g20': route20,'g21': route21,'g22': route22,'g23':route23, 'g24':route24,'g25':route25,'g26':route26,'g27':route27,'g28':route28,'g29':route29,
                          'g30':route30,'g31':route31,'g32':route32,'g33':route33,'g34':route34,'g35':route35,'g36':route36};
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
