window.onload = function () {
    $(function () {
        InitBoardSquares();
        console.log("Main Init Called");
    });
};

function InitBoardSquares() {
    var light = 1;
    var rankName;
    var fileName;
    var divString;
    var rankIter;
    var fileIter;
    var lightString;
    var hide;

    for (rankIter = 11; rankIter >= 0; rankIter--) {
        light ^= 1;
        rankName = "rank" + (rankIter + 1);
        for (fileIter = 0; fileIter <= 11; fileIter++) {
            fileName = "file" + (fileIter + 1);
            if (light == 0) lightString = "Light";
            else lightString = "Dark";
            if ((rankIter < 2 && fileIter < 2) || (rankIter < 2 && fileIter > 9) || (rankIter > 9 && fileIter < 2) || (rankIter > 9 && fileIter > 9))
                hide = "hidden";
            else
                hide = "";
            light ^= 1;
            divString = "<div class=\"Square " + rankName + " " + fileName + " " + lightString + " " + hide + "\"/>";
            $("#Board").append(divString);
        }
    }

}