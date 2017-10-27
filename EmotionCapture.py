# -*- coding: utf-8 -*-
import cognitive_face as CF
import numpy as np
import cv2
import Emotion as ET
import http.client, urllib.request, urllib.parse, urllib.error, base64, json
from PIL import Image, ImageDraw, ImageFont
import settings

cap = cv2.VideoCapture(0)

while(True):
    # フレームをキャプチャする
    ret, frame = cap.read()

    # 画面に表示する
    cv2.imshow('frame',frame)

    # キーボード入力待ち
    key = cv2.waitKey(1) & 0xFF

    # qが押された場合は終了する
    if key == ord('q'):
        break
    # sが押された場合は保存する
    if key == ord('s'):
        path = "photo.jpg"
        cv2.imwrite(path,frame)


KEY = settings.FACE_AP
CF.Key.set(KEY)

BASE_URL = settings.FACE_AP_URL  # Replace with your regional Base URL
CF.BaseUrl.set(BASE_URL)


img_url = 'photo.jpg'
result = CF.face.detect(img_url, face_id=True, landmarks=True, attributes='age,gender')
print(result)
facedata = result[0]
print("\n\n")
print (facedata['faceAttributes']['age'], "歳でした")


img = 'photo.jpg' #ここはイメージのファイルを指定してください。
data = open(img,'br')
a = ET.emotion(data)
json_dict = json.loads(a)
print(json.dumps(json_dict, sort_keys=True, indent=1))	
print("main Start!!")
for x in json_dict:
        top = x['faceRectangle']['top']
        left =  x['faceRectangle']['left']
        width = x['faceRectangle']['width']
        height =  x['faceRectangle']['height']
        
        anger = x['scores']['anger']
        contempt = x['scores']['contempt']
        disgust = x['scores']['disgust']
        fear = x['scores']['fear']
        happiness = x['scores']['happiness']
        neutral = x['scores']['neutral']
        sadness = x['scores']['sadness']
        surprise = x['scores']['surprise']
    
strText =['']*8
start=(left,top)
end=(left+width,left + width + height)

strText[0] = '怒り：' + str(anger)
strText[1] = '軽蔑：' + str(contempt)
strText[2] = '嫌悪：' + str(disgust)
strText[3] = '恐れ：' + str(fear)
strText[4] = '幸福：' + str(happiness)
strText[5] = '通常：' + str(neutral)
strText[6] = '悲哀：' + str(sadness)
strText[7] = '驚き：' + str(surprise)
startText=(left+width+20,top)


