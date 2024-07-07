// -----------------------------------------------------------------------//
// PAP - CRONOMETRO DA PISTA COM LIGAÇÃO A BASE DE DADOS - ENTROBOTS
// Diogo Miguel
// Nº 14056 - 3º GPSI
// -----------------------------------------------------------------------//

// ....................................... ENTROBOTS ........................................... // 
// ............................................................................................ // 
// .........  RC522 ----------> ESP32    .............  LCD I2C -----> ESP32     .............. // 
// .........  SDA     ------    GPIO 5   .............  SDA   -----   GPIO 21    .............. //
// .........  SCK     ------    GPIO 18  .............  SCL   -----   GPIO 22    .............. //
// .........  MOSI    ------    GPIO 23  .............  GND   -----   GND        .............. //
// .........  MISOI   ------    GPIO 19  .............  VCC   -----   5V         .............. //
// .........  GND     ------    GND      ....................................... .............. //
// .........  RST     ------    GPIO 2   .............  Sens Obst -----> ESP32   .............. //
// .........  VCC     ------    3.3V     .............  DOUT  -----  GPIO 25/26  .............. //
// ...................................................  GND   -----  GND         .............. //
// .........  BOTÕES ----> ESP32 .....................  VCC   -----  5V          .............. //
// .......... PIN  ------  32/33 .............................................................. // 
// .......... GND  ------  GND   .............................................................. // 
// ............................................................................................ // 

// --- BIBLIOTECAS ---//

#include <WiFi.h>          
#include <HTTPClient.h>  
#include <WebServer.h>    
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <MFRC522.h>

LiquidCrystal_I2C displayCronometro(0x27, 20, 4);

#define SS_PIN 5
#define RST_PIN 2
MFRC522 mfrc522(SS_PIN, RST_PIN);

#define ON_Board_LED 4

// --- ROOTER --- //
const char* ssid = "Name-Rooter";
const char* password = "Pass-Rooter";
const char* serverAddress = "Ip-pc-Xamp";

// --- VARIAVEIS --- //

int sensorPartida = 26;
int sensorChegada = 25;
int botaoReset = 32;
bool comecou = false;
bool pausado = false;
long millisNaPartida;
long deltaTempo;
int botaoTag = 33;

String UIDresultSend;
String email;
String payload;

bool tagDetectada = false;

WebServer server(80);

int readsuccess;
byte readcard[4];
char str[32] = "";
String StrUID;

void setup()  // --- INICIALIZAÇÃO DOS COMPONENTES E LIGAÇÃO AO WIFI --- //
{
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  displayCronometro.init();
  displayCronometro.setBacklight(HIGH); 
  pinMode(sensorPartida, INPUT);
  pinMode(sensorChegada, INPUT);
  pinMode(botaoReset, INPUT_PULLUP);
  pinMode(botaoTag, INPUT_PULLUP);

  delay(500);

  WiFi.begin(ssid, password);
  Serial.println("");

  pinMode(ON_Board_LED, OUTPUT);
  digitalWrite(ON_Board_LED, HIGH);

  Serial.print("Connecting");
  while (WiFi.status() != WL_CONNECTED) 
  {
    Serial.print(".");
    digitalWrite(ON_Board_LED, LOW);
    delay(250);
    digitalWrite(ON_Board_LED, HIGH);
    delay(250);
  }
  digitalWrite(ON_Board_LED, HIGH);
  
  Serial.println("");
  Serial.print("Successfully connected to : ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  displayCronometro.clear();
  displayCronometro.setCursor(3, 1);
  displayCronometro.print("Passe uma TAG");
  Serial.println("Passe uma TAG:");
  Serial.println("");
}

void contaTempoExibeTempo() // --- CRONOMETRAGEM --- //
{
  if (comecou && !pausado) 
  {
    deltaTempo = millis() - millisNaPartida;
    int minutos = deltaTempo / 60000;
    int segundos = (deltaTempo / 1000) % 60;
    int milissegundos = (deltaTempo / 10) % 100;

    displayCronometro.setCursor(5, 1);
    displayCronometro.print("Cronometro");
    displayCronometro.setCursor(6, 2);
    if (minutos < 10) displayCronometro.print("0");
    displayCronometro.print(minutos);
    displayCronometro.print(":");
    if (segundos < 10) displayCronometro.print("0");
    displayCronometro.print(segundos);
    displayCronometro.print(":");
    if (milissegundos < 10) displayCronometro.print("0");
    displayCronometro.print(milissegundos);
  }
}

void resetaCronometro() // --- RESETAR O CRONOMETRO --- //
{
  comecou = false;
  pausado = false;
  deltaTempo = 0;
  displayCronometro.clear();
  displayCronometro.setCursor(5, 1);
  displayCronometro.print("Cronometro");
  displayCronometro.setCursor(6, 2);
  displayCronometro.print("00:00:00");
  delay(1000);
}

int getid(String UIDresultSend) // --- ENVIAR O ID PARA O WEBSITE --- //
{
  if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial()) // --- GERAR UM ID PARA A TAG --- //
  {
    return 0;
  }

  StrUID = "";

  for (int i = 0; i < mfrc522.uid.size; ++i) 
  {
    StrUID += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
    StrUID += String(mfrc522.uid.uidByte[i], HEX);
  }

  mfrc522.PICC_HaltA();
  HTTPClient http;
  String url = "http://" + String(serverAddress) + "/ENTROBOTS/verificaTag.php"; // --- VERIFICA SE EXISTE ESSA TAG CONECTADA A ALGUM EMAIL --- //
  String postData = "UIDresult=" + StrUID;
  http.begin(url);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  int httpCode = http.POST(postData);

  if (httpCode > 0) 
  {
    if (httpCode == HTTP_CODE_OK) 
    {
      email = http.getString();

        // Envio do UID para o site
      readsuccess = 1;
      if (readsuccess) 
      {
        HTTPClient http;
        String UIDresultSend, postData;
        UIDresultSend = StrUID;
        postData = "UIDresult=" + UIDresultSend;
        String url = "http://" + String(serverAddress) + "/ENTROBOTS/getUID.php"; // --- ENVIA O ID PARA O WEBSITE --- //
        http.begin(url);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");
        int httpCode = http.POST(postData);
        String payload = http.getString();
        Serial.println("UID sent: " + UIDresultSend);
        Serial.println("Email sent: " + email);
        Serial.println("HTTP Response code: " + String(httpCode));
        Serial.println("Server response: " + payload);
        //delay(1000);
        http.end();
      }

      if (email != "Nenhum email encontrado para este UID") 
      {
        Serial.println("Email associado a este UID: " + email);
        const int maxCaracteres = 20;
        String emailExibicao = email.substring(0, maxCaracteres);

        displayCronometro.clear();
        displayCronometro.setCursor(0, 0);
        displayCronometro.print("TAG: " + StrUID);
        displayCronometro.setCursor(0, 1);
        displayCronometro.print("Email associado: ");
        displayCronometro.setCursor(0, 2);
        displayCronometro.print(emailExibicao); 
        delay(3000);

        return 1; // Tag encontrada
      } 
      else 
      {
        Serial.println("Nenhum email encontrado para este UID");
        displayCronometro.clear();
        displayCronometro.setCursor(0, 2);
        displayCronometro.print("TAG nao registada");
        delay(2000);

        return -1; // Tag não registrada
      }
    }
  } 
  else 
  {
    Serial.println("Falha na requisição HTTP");
  }

  http.end();
  return 0; // Erro na requisição
}

void registarTempo(String email, long deltaTempo) // --- REGISTA O TEMPO E ENVIA PARA A BASE DE DADOS --- //
{
  if (pausado) 
  {
    String tempo = String(deltaTempo);

    String postData = "email=" + email + "&tempo=" + tempo;

    HTTPClient http;
    String url = "http://" + String(serverAddress) + "/ENTROBOTS/RegistarTempo.php";
    http.begin(url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpCode = http.POST(postData);
    String payload = http.getString();
    http.end();

    Serial.println("Tempo registrado para UID: " + email);
    Serial.println("Tempo registrado :" + tempo);
    Serial.println("HTTP Response code: " + String(httpCode));
    Serial.println("Server response: " + payload);
  }
}

void tagnova() // --- DETETAR UM TAG NOVA --- //
{
  displayCronometro.clear();
  displayCronometro.setCursor(1, 1);
  displayCronometro.print("Passe uma nova TAG");
  delay(2000); // Ajuste o tempo de espera conforme necessário

  tagDetectada = false;
  comecou = false;
  pausado = false;
  deltaTempo = 0;
}

void loop()
{
  if (!tagDetectada) // --- SE NÃO EXISTIR TAG REGISTADA NA BASE DE DADOS NÃO COMEÇA O CRONOMETRO --- //
  {
    int readsuccess = getid(UIDresultSend);
    if (readsuccess == 1) {
      digitalWrite(ON_Board_LED, LOW);
      delay(3000); // Espera 3 segundos após exibir informações da tag
      tagDetectada = true;

      displayCronometro.clear();
      displayCronometro.setCursor(5, 1);
      displayCronometro.print("Cronometro");
      displayCronometro.setCursor(6, 2);
      displayCronometro.print("00:00:00");

      digitalWrite(ON_Board_LED, HIGH);
    } 
    else if (readsuccess == -1) 
    {
      tagnova();
    }
  }

  if (tagDetectada) 
  {
    if (digitalRead(sensorPartida) == LOW && !comecou && !pausado) // --- DETETA O SENSOR DA PARTIDA --- //
    {
      comecou = true;
      millisNaPartida = millis();
      displayCronometro.clear();
      displayCronometro.setCursor(5, 1);
      displayCronometro.print("Cronometro");
      displayCronometro.setCursor(6, 2);
      displayCronometro.print("00:00:00");
    }

    if (digitalRead(sensorChegada) == LOW && comecou && !pausado)  // --- DETETA O SENSOR DA CHEGADA --- //
    {
      pausado = true;
      registarTempo(email, deltaTempo);
    }

    if (comecou && !pausado) // --- JA ESTA A CRONOMETRAR E CHAMA A FUNÇÃO PARA MOSTRAR O TEMPO DO CROMETRO --- //
    {
      contaTempoExibeTempo();
    }

    if (digitalRead(botaoReset) == LOW && comecou && pausado) // --- AO CLICAR NO BOTAO RESETA O CRONOMETRO --- //
    {
      resetaCronometro();
    }

    if (digitalRead(botaoTag) == LOW && pausado) // --- AO CLICAR NO BOTAO PODE PASSAR UMA NOVA TAG --- //
    {
      tagnova();
    }
  }
}
