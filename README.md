# LearoBot - AI Telegram Bot

## Overview
### Train:
There are three main tables:
+ words
+ replies
+ reply_word

If person1 replies person2, the words of person2's message are stored in **words** table.

The reply is stored in **replies** table.

After all, reply will be attached to all of the words.

### Predict:
Finds the words of a sentence from words table. Then get the most relevant reply for those words using this formula:

repeat of a reply to a word = a
count of words of the sentence, that reply refers to them = b

a is much more important than b. Because two persons replied the same to a sentence. so it has about 60% importance.
b has 40% importance.

(60% * SUM(a)) + (40% * b) = score

If score is more than a certain value, it should send the reply to user.
