(function ($) {

    var typeInterval;
    var typingInitialScreen = true;

    $.fn.termLink = function () {

    };

    function typify($element, $speed) {
        $speed = $speed || 200;

        var letters = $element.find("span");

        var currentLetter = 0;
        typeInterval = setInterval(function () {
            $(letters[currentLetter]).css("display", "inline-block");
            currentLetter++;

            if (currentLetter === letters.length) {
                clearInterval(typeInterval);
                if (typingInitialScreen) {
                    writeToConsole($(".highlight").text());
                }
                typingInitialScreen = false;
                return true;
            }
        }, $speed);
    }

    function moveCursor($direction) {
        var $currentTarget = $(".highlight");
        var spanIndex = $currentTarget.index();
        var $parentRow = $currentTarget.closest(".row");
        var emClass = $currentTarget.closest("em").attr("class");

        clearInterval(typeInterval);
        $(".grid span").css("display", "inline-block");

        switch ($direction) {
            //down
            case 115:
                var $nextRow = $parentRow.next(".row");
                if ($nextRow.length !== 0) {
                    $(".highlight").removeClass("highlight");
                    $nextRow.find("." + emClass).find("span:eq(" + spanIndex + ")").addClass("highlight");
                }
                break;
            //right
            case 100:
                var $nextSpan = $currentTarget.next("span");
                if ($nextSpan.length !== 0) {
                    $(".highlight").removeClass("highlight");
                    $nextSpan.addClass("highlight");
                } else if (emClass === "column-l") {
                    $(".highlight").removeClass("highlight");
                    $parentRow.find(".column-r").find("span").first().addClass("highlight");
                }
                break;
            //up
            case 119:
                var $prevRow = $parentRow.prev(".row");
                if ($prevRow.length !== 0) {
                    $(".highlight").removeClass("highlight");
                    $prevRow.find("." + emClass).find("span:eq(" + spanIndex + ")").addClass("highlight");
                }
                break;
            //left
            case 97:
                var $prevSpan = $currentTarget.prev("span");
                if ($prevSpan.length !== 0) {
                    $(".highlight").removeClass("highlight");
                    $prevSpan.addClass("highlight");
                } else if (emClass === "column-r") {
                    $(".highlight").removeClass("highlight");
                    $parentRow.find(".column-l").find("span").last().addClass("highlight");
                }
                break;
            //Enter or space
            case 13:
            case 32:
                if (!typingInitialScreen) {
                    var lineType = ""
                    if ($(".highlight").hasClass("spec-seq-start") && $(".highlight--extra").length > 0) {
                        $(".highlight").removeClass("spec-seq-start");
                        lineType = "spec-seq";
                    }
                    if ($(".highlight").hasClass("word")) {
                        lineType = "word";
                    }
                    addConsoleLine($("#consoleLine").find("em").text(), false);

                    if (lineType !== "") {
                        $.ajax({
                            url: "ajaxHandler.php",
                            type: "POST",
                            data: {
                                inputString: $("#consoleLine").find("em").text(),
                                lineType: lineType
                            },
                            success: function (result) {
                                if (result.returnType === "spec-seq-remove") {
                                    var wordToRemove = result.misc.wordToRemove;
                                    $("[data-word='" + wordToRemove + "'").removeClass().removeAttr("data-word").text(".");
                                }

                                if (result.returnType === "spec-seq-reset") {
                                    var $attempts = $(".attempts");
                                    $attempts.data("attempts", 4);
                                    renderAttempts();
                                    $attempts.find("span").each(function () {
                                        $(this).css("display", "inline-block");
                                    });
                                }

                                if (result.returnType === "word") {
                                    if (result.misc.attempt === "failed") {
                                        var $attempts = $(".attempts");
                                        var remaining = $attempts.data("attempts");
                                        $attempts.data("attempts", remaining - 1);
                                        renderAttempts();
                                        $attempts.find("span").each(function () {
                                            $(this).css("display", "inline-block");
                                        });

                                        if (remaining === 1) {
                                            addConsoleLine("Entry Denied.", false);
                                            addConsoleLine("Init Lockout", false);
                                            $(".highlight").removeClass("highlight");
                                            $(".highlight--extra").removeClass("highlight--extra");
                                            return false;
                                        }
                                    } else if (result.misc.attempt === "success") {
                                        $(".highlight").removeClass("highlight");
                                        $(".highlight--extra").removeClass("highlight--extra");
                                    }
                                }

                                var returnString = result.returnString.split("|");
                                $.each(returnString, function (key, value) {
                                    addConsoleLine(value, false);
                                });
                            }
                        });
                    }
                }
                break;
        }

        $(".highlight--extra").removeClass("highlight--extra");
        var $newTarget = $(".highlight");

        var consoleOutput = $newTarget.text();

        if ($newTarget.hasClass("spec-seq-start")) {
            var seqChar = $newTarget.data("char");
            var hasEndPoint = false;
            var endPoint = null;
            $newTarget.nextAll().each(function () {
                if ($(this).hasClass("word")) {
                    return false;
                }

                if ($(this).hasClass("spec-seq-end") && $(this).data("char") === seqChar) {
                    hasEndPoint = true;
                    endPoint = $(this);
                    return false;
                }
            });

            if (hasEndPoint) {
                $newTarget.nextUntil(endPoint).addClass("highlight--extra");
                endPoint.addClass("highlight--extra");

                $(".highlight--extra").each(function () {
                    consoleOutput += $(this).text();
                });
            }
        }

        if ($newTarget.hasClass("word")) {
            var wordNr = $newTarget.data("word");
            $("[data-word='" + wordNr + "'").addClass("highlight--extra");

            consoleOutput = "";

            $(".column-l .highlight--extra").each(function () {
                consoleOutput += $(this).text();
            });

            $(".column-r .highlight--extra").each(function () {
                consoleOutput += $(this).text();
            });
        }

        if ($currentTarget !== $newTarget) {
            writeToConsole(consoleOutput);
        }
    }

    function writeToConsole(input) {
        var $consoleLine = $("#consoleLine");
        var output = wrapCharacters(input);
        $consoleLine.find("em").html(output);
        typify($consoleLine.find("em"), 10);
    }

    function addConsoleLine(input, _typify) {
        var $newLine = $(document.createElement("div")).addClass("row");
        var newLineText = _typify ? wrapCharacters(input) : input;
        $newLine.insertBefore($("#consoleLine"));
        $newLine.html(">" + newLineText);
        if (_typify) {
            typify($newLine, 10);
        }
    }

    function wrapCharacters(input) {
        var output = "";

        var text = input.split("");

        $.each(text, function (i, el) {
            output += "<span>" + el + "</span>";
        });

        return output;
    }

    function renderAttempts() {
        var $attempts = $(".attempts");
        $attempts.html("");
        var remaining = $attempts.data("attempts");

        for ($i = 0; $i < remaining; $i++) {
            var $attemptBlock = $(document.createElement("span")).addClass("attempt-block");
            $attemptBlock.appendTo($attempts);
        }
    }

    $(document).ready(function () {
        $('body').termLink();

        renderAttempts();

        typify($(".grid"), 10);

        $("#entries").find("em").first().find("span").first().addClass("highlight");

        $(document).keypress(function (e) {
            moveCursor(e.which);
        });
    });

})(jQuery);