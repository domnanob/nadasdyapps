export default class Word {
    constructor() {
        this.words;
    }
    async getRandomWord() {
        let w = ""
        await fetch("resources/magyar-szavak.txt")
            .then((res) => res.text())
            .then((text) => {
                this.words = text.split('\n')
                w = this.words[Math.floor(Math.random() * this.words.length)]
            }).catch((e) => console.error(e));
        return w
    }
}

const badEmotes = ['128528', '128530', '128532', '128550', '128557', '128563', '128564', '128578', '129300', '129317', '128566', '128534', '128529', '128517','128520']
const goodEmotes = ['128519', '128521', '128525', '128527', '128536', '129297', '129321', '129396', '129299', '128558', '128524', '128526','129327']

const badQuotes = ['Egyre melegebb, ja nem', 'Próblákozol egyáltalán?', 'Ez mellé ment','Talán a másik?','Szerintem add fel','Kezdek unatkozni','Amúgy ügyes vagy','Végre! Ja nem..','Egyre messzebb vagy','Ez nem lett jó','Nem talált','Majd legközelebb','Szerintem add fel']
const goodQuotes = ['Sikerült!','Király!','Már nincs sok hátra!','Egyre közelebb!','Ez is jó!','Te csalsz?','Rajta vagy!','Helyes!','Csak így tovább!','Ezzaaazz!','Tudod te..','Yepeee!','Ez már megvan!' ]

const secretWord = "LILI"

function getGoodQuote() {
    return goodQuotes[Math.floor(Math.random() * goodQuotes.length)] + " " + String.fromCodePoint(goodEmotes[Math.floor(Math.random() * goodEmotes.length)])
}
function getBadQuote() {
    return badQuotes[Math.floor(Math.random() * badQuotes.length)] + " " + String.fromCodePoint(badEmotes[Math.floor(Math.random() * badEmotes.length)])   
}
export {getGoodQuote, getBadQuote, secretWord}