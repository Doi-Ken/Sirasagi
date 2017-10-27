import http.client, urllib.request, urllib.parse, urllib.error, base64, json
from PIL import Image, ImageDraw, ImageFont
import settings

def emotion(image):
	headers = {
	    # Request headers
	    'Content-Type': 'application/octet-stream',
	    'Ocp-Apim-Subscription-Key': settings.EMOTION_AP,
	}
	
	params = urllib.parse.urlencode({
	})
	
	try:
	    conn = http.client.HTTPSConnection(settings.EMOTION_AP_URL)
	    conn.request("POST", "/emotion/v1.0/recognize", image, headers)
	    response = conn.getresponse()
	    data = response.read().decode('utf-8')
	    conn.close()
	    return data
	    
	except Exception as e:
	    print("[Errno {0}] {1}".format(e.errno, e.strerror))


def drawSquare(canvas,start,end):
	
    lineColor =(0,0,255,255)
    lineSize = 10
    canvas.paste(data,(0,0)) 
    draw = ImageDraw.Draw(canvas)
    draw.line(((start[0],start[1]),(start[0],end[1])),lineColor,lineSize)
    draw.line(((start[0],end[1]),(end[0],end[1])),lineColor,lineSize)
    draw.line(((end[0],end[1]),(end[0],start[1])),lineColor,lineSize)
    draw.line(((end[0],start[1]),(start[0],start[1])),lineColor,lineSize)
    return canvas


def drawText(canvas,start,strText):
   
   #日本語フォントはIPAが公開しているフォントを使いました。
   draw = ImageDraw.Draw(canvas)
   draw.font = ImageFont.truetype('IPAfont/ipag.ttf', 60) #フォントファイルの指定
   draw.text((start[0],start[1]), strText[0], (0,0,255))
   draw.text((start[0],start[1]+65), strText[1], (0,0,255))
   draw.text((start[0],start[1]+130), strText[2], (0,0,255))
   draw.text((start[0],start[1]+195), strText[3], (0,0,255))
   draw.text((start[0],start[1]+260), strText[4], (0,0,255))
   draw.text((start[0],start[1]+325), strText[5], (0,0,255))
   draw.text((start[0],start[1]+390), strText[6], (0,0,255))
   draw.text((start[0],start[1]+455), strText[7], (0,0,255))

   return canvas


if __name__ == '__main__':
    img = 'photo.jpg' #ここはイメージのファイルを指定してください。
    
    data = open(img,'br')
    a = emotion(data)
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
    startText=(left+width+20,top);

    data = Image.open(img,'r')
    canvas = Image.new('RGBA', (data.size[0], data.size[1]), (255, 255, 255,0))
    canvas = drawSquare(canvas,start,end)
    canvas = drawText(canvas,startText,strText)
    canvas.show()
    canvas.save('photo2.jpg', 'JPEG', quality=100, optimize=True) #解析結果をファイルに出力
