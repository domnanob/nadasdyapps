export default class Gallows {
    constructor(canvas) {
        this.canvas = canvas
        this.ctx = canvas.getContext("2d")
        this.ctx.lineWidth = 10
        this.errorCount = 0
        this.ctx.lineCap = "round";
    }
    setColor(color) {
        this.ctx.strokeStyle = color;
    }
    printGround() {
        this.ctx.beginPath();
        this.ctx.moveTo(150, 480);
        this.ctx.lineTo(350, 480);
        this.ctx.stroke();
    }
    printGallowsUp() {
        this.ctx.beginPath();
        this.ctx.moveTo(250, 480);
        this.ctx.lineTo(250, 255);
        this.ctx.stroke();
    }
    printGallowsRight() {
        this.ctx.beginPath();
        this.ctx.moveTo(250, 255);
        this.ctx.lineTo(350, 255);
        this.ctx.stroke();
    }
    printGallowsDown() {
        this.ctx.beginPath();
        this.ctx.moveTo(350, 255);
        this.ctx.lineTo(350, 295);
        this.ctx.stroke();
    }
    printHumanHead() {
        this.ctx.beginPath();
        this.ctx.moveTo(370, 320);
        this.ctx.arc(350, 320, 20, 0, 2 * Math.PI);
        this.ctx.stroke();
    }
    printHumanBody() {
        this.ctx.beginPath();
        this.ctx.moveTo(350, 340);
        this.ctx.lineTo(350, 400);
        this.ctx.stroke();
    }
    printHumanHandsLeft() {
        this.ctx.beginPath();
        this.ctx.moveTo(320, 360);
        this.ctx.lineTo(350, 360);
        this.ctx.stroke();
    }
    printHumanHandsRight() {
        this.ctx.beginPath();
        this.ctx.moveTo(350, 360);
        this.ctx.lineTo(380, 360);
        this.ctx.stroke();
    }
    printHumanFootLeft() {
        this.ctx.beginPath();
        this.ctx.moveTo(350, 400);
        this.ctx.lineTo(330, 450);
        this.ctx.stroke();
    }
    printHumanFootRight() {
        this.ctx.beginPath();
        this.ctx.moveTo(350, 400);
        this.ctx.lineTo(370, 450);
        this.ctx.stroke();
    }
    printAll() {
        this.printGround()
        this.printGallowsUp()
        this.printGallowsRight()
        this.printGallowsDown()
        this.printHumanHead()
        this.printHumanBody()
        this.printHumanHandsLeft()
        this.printHumanHandsRight()
        this.printHumanFootLeft()
        this.printHumanFootRight()
    }
    printError() {
        this.errorCount++
        switch (this.errorCount) {
            case 1:
                this.printGround()
                break;
            case 2:
                this.printGallowsUp()
                break;
            case 3:
                this.printGallowsRight()
                break;
            case 4:
                this.printGallowsDown()
                break;
            case 5:
                this.printHumanHead()
                break;
            case 6:
                this.printHumanBody()
                break;
            case 7:
                this.printHumanHandsLeft()
                break;
            case 8:
                this.printHumanHandsRight()
                break;
            case 9:
                this.printHumanFootLeft()
                break;
            case 10:
                this.printHumanFootRight()
                break;
        }
        return this.errorCount >= 10
    }
}