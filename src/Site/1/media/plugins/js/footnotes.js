/**
 * @version        1.0
 * @author        Timothy Groves    desk [at] brandspankingnew.net
 * @package        formatFootnotes JavaScript Standard Version * @since        26/01/2006
 */

var articles = 0;

function formatFootnotes(contID, noteID) {
    // check for DOM capabilities
    if (!document.getElementById)
        return false;

    var cont = document.getElementById(contID);
    var noteholder = document.getElementById(noteID);
    var spans = cont.getElementsByTagName("span");

    var notes = 0;
    articles++;

    for (i = 0; i < spans.length; i++) {
        if (spans[i].className == "footnote") {
            notes++;

            // get content of span
            var noteNode = spans[i].cloneNode(true);

            // remove css styling
            noteNode.className = "";

            // create a new div to hold the footnote
            var newEle = document.createElement("div");

            newEle.appendChild(document.createTextNode(notes + ". "));
            newEle.appendChild(noteNode);

            // add backlink
            blink = document.createElement("a");
            blink.href = "#ftnlink" + articles + "_" + notes;
            blink.appendChild(document.createTextNode(" [back]"));
            newEle.appendChild(blink);

            noteholder.appendChild(newEle);

            // add id & style
            noteholder.lastChild.id = "ftn" + articles + "_" + notes;
            noteholder.lastChild.className = "footnote";


            // insert link into span
            var newEle = document.createElement("a");
            newEle.href = "#" + noteID;
            newEle.title = "show footnote";
            newEle.id = "ftnlink" + articles + "_" + notes;
            newEle.className = "ftnlink";

            newEle.appendChild(document.createTextNode(notes));

            // empty span
            while (spans[i].childNodes.length)
                spans[i].removeChild(spans[i].firstChild);

            spans[i].appendChild(newEle);
        }
    }

}
