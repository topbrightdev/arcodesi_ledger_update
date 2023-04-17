import os
import time
import csv
import undetected_chromedriver as uc
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

co = uc.ChromeOptions()

co.add_argument('--disable-infobars')
co.add_argument('--disable-extensions')
co.add_argument('--profile-directory=Default')
co.add_argument("--incognito")
# co.add_argument("--headless")
co.add_argument("--disable-plugins-discovery")
co.add_argument("--start-maximized")
co.add_argument("--no-sandbox")  # bypass OS security model
co.add_argument("--disable-dev-shm-usage")
co.add_argument("--disable-popup-blocking")
co.add_argument('--disable-blink-features=AutomationControlled')

skill_set = ['React', 'Laravel', 'PHP', 'Next.js', 'Vue.js', 'ASP.NET', 'JavaScript', 'TypeScript', 'Python', 'Node.js', 'ExpressJS', 'MySQL', 'MongoDB', 'PostgreSQL', 'GraphQL']
user_info = {}
with open('address1.csv', 'r') as f:
    red = csv.DictReader(f)
    for d in red:
        user_info.setdefault(d['key'], d['status'])

with open('summary.txt') as fs:
    summary = fs.readlines()


def main():
    driver = uc.Chrome(options=co)
    driver.maximize_window()
    user_first_name = user_info["first_name"]
    user_last_name = user_info["last_name"]
    user_email = user_info["email"]
    user_password = user_info["password"]
    country = user_info["country"]
    address = user_info["address"]
    city = user_info["city"]
    phone = user_info["phone"]
    my_role = user_info["role"]
    my_rate = user_info["rate"]
    apt = user_info["apt"]
    my_state = user_info["state"]
    try:
        driver.get("https://www.upwork.com/nx/signup/?dest=home")
        work_button = driver.find_elements(By.CLASS_NAME, "up-radio")[1]
        driver.execute_script("arguments[0].click();", work_button)
        apply_button = driver.find_element(By.CLASS_NAME, "up-btn-primary")
        apply_button.click()
        time.sleep(1)
        element = WebDriverWait(driver, 10).until(
            EC.visibility_of_element_located((By.ID, "first-name-input"))
        )
        first_name_input = driver.find_element(By.ID, "first-name-input")
        first_name_input.send_keys(user_first_name)
        second_name_input = driver.find_element(By.ID, "last-name-input")
        second_name_input.send_keys(user_last_name)
        email_input = driver.find_element(By.ID, "redesigned-input-email")
        email_input.send_keys(user_email)
        password_input = driver.find_element(By.ID, "password-input")
        password_input.send_keys(user_password)
        select_country = driver.find_element(By.ID, "country-dropdown")
        select_country.click()
        time.sleep(2)
        dropdown = driver.find_element(By.ID, "dropdown-menu-5")
        li_elems = dropdown.find_elements(By.CLASS_NAME, "up-menu-item")
        for elem in li_elems:
            if elem.text == country:
                driver.execute_script("arguments[0].click();", elem)
                break
        checkbox1 = driver.find_element(By.ID, "checkbox-promo")
        driver.execute_script("arguments[0].click();", checkbox1)
        checkbox2 = driver.find_element(By.ID, "checkbox-terms")
        driver.execute_script("arguments[0].click();", checkbox2)
        submit_btn = driver.find_element(By.ID, "button-submit-form")
        driver.execute_script("arguments[0].click();", submit_btn)
        time.sleep(20)
        try:
            element = WebDriverWait(driver, 300).until(
                EC.visibility_of_element_located((By.CLASS_NAME, "welcome-step1-list"))
            )
            started_btn = driver.find_element(By.CLASS_NAME, "air3-btn-primary")
            driver.execute_script("arguments[0].click();", started_btn)
            try:
                sections = WebDriverWait(driver, 10).until(
                    EC.visibility_of_element_located((By.CLASS_NAME, "air3-btn-box-label"))
                )
                expert_section = driver.find_elements(By.CLASS_NAME, "air3-btn-box-label")[2]
                driver.execute_script("arguments[0].click();", expert_section)
                next_btn = driver.find_element(By.CLASS_NAME, "air3-btn-primary")
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
            try:
                sections = WebDriverWait(driver, 10).until(
                    EC.visibility_of_element_located((By.CLASS_NAME, "air3-btn-box-label"))
                )
                full_time_section = driver.find_elements(By.CLASS_NAME, "air3-btn-box-label")[2]
                driver.execute_script("arguments[0].click();", full_time_section)
                next_btn = driver.find_element(By.CLASS_NAME, "air3-btn-primary")
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
            try:
                sections = WebDriverWait(driver, 10).until(
                    EC.visibility_of_element_located((By.CLASS_NAME, "air3-btn-box-label"))
                )
                offer_sections = driver.find_elements(By.CLASS_NAME, "air3-btn-box-label")
                for offer_section in offer_sections:
                    driver.execute_script("arguments[0].click();", offer_section)
                checkbox3 = driver.find_element(By.CLASS_NAME, "air3-checkbox-input")
                driver.execute_script("arguments[0].click();", checkbox3)
                next_btn = driver.find_element(By.CLASS_NAME, "air3-btn-primary")
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(5)
            except Exception as e:
                print(e)
                pass
            # upload resume page
            try:
                resume_btn = driver.find_element(By.XPATH,
                                                 '//*[@id="main"]/div/div/div[2]/div/div/div/div[2]/div/div/div[3]/div/div[2]/div/div[1]/button[2]')
                driver.execute_script("arguments[0].click();", resume_btn)
                file_input = driver.find_element(By.XPATH,
                                                 '/html/body/div[5]/div[2]/div/div[1]/div[2]/div/div[2]/div/div/div/div/p/span/input')
                file_input.send_keys(os.getcwd() + "/resume.pdf")
                time.sleep(8)
                continue_btn = driver.find_elements(By.CLASS_NAME, 'air3-btn-primary')[-1]
                driver.execute_script("arguments[0].click();", continue_btn)
            except Exception as e:
                print(e)
                pass
            # Professional role page
            try:
                sections = WebDriverWait(driver, 10).until(
                    EC.visibility_of_element_located((By.ID, "title-label"))
                )
                role_input = driver.find_element(By.CLASS_NAME, "air3-input")
                role_input.clear()
                time.sleep(1)
                role_input.send_keys(my_role)
                time.sleep(1)
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                next_btn.click()
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
            # experience page
            try:
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
            #     education page
            try:
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
            # language page
            try:
                select_lan = driver.find_element(By.CLASS_NAME, "air3-dropdown-toggle-title")
                select_lan.click()
                time.sleep(2)
                dropdown = driver.find_element(By.CLASS_NAME, "air3-menu-list")
                li_elem = dropdown.find_elements(By.CLASS_NAME, "air3-menu-item")[3]
                driver.execute_script("arguments[0].click();", li_elem)
                time.sleep(1)
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
            #     skills page
            try:
                for skill in skill_set:
                    skill_input = driver.find_element(By.CLASS_NAME, 'air3-input')
                    driver.execute_script("arguments[0].click();", skill_input)
                    time.sleep(1)
                    fake_input = driver.find_elements(By.CLASS_NAME, 'air3-input')[-1]
                    fake_input.send_keys(skill)
                    time.sleep(2)
                    skill_dropdown = driver.find_element(By.CLASS_NAME, "air3-menu-list")
                    skill_item = skill_dropdown.find_elements(By.CLASS_NAME, "air3-menu-item")[0]
                    driver.execute_script("arguments[0].click();", skill_item)
                    time.sleep(1)
            except Exception as e:
                print(e)
                pass

            try:
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass

            #     tell me yourself page
            try:
                myself_text = driver.find_element(By.CLASS_NAME, "air3-textarea")
                myself_text.clear()
                time.sleep(1)
                myself_text.send_keys(summary)
                time.sleep(5)
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                next_btn.click()
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
        #     select service page
            try:
                drop_down = driver.find_element(By.CLASS_NAME, "air3-dropdown-toggle")
                driver.execute_script("arguments[0].click();", drop_down)
                time.sleep(2)
                software_elem = driver.find_element(By.XPATH, '//*[@id="dropdown-menu-45"]/li[11]')
                driver.execute_script("arguments[0].click();", software_elem)
                time.sleep(1)
                dropdown = driver.find_element(By.XPATH, '//*[@id="dropdown-menu-45"]/li[11]/div[2]/ul')
                li_elems = dropdown.find_elements(By.CLASS_NAME, "air3-menu-item")
                for key, elem in enumerate(li_elems):
                    if key != 7:
                        driver.execute_script("arguments[0].click();", elem)
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
            #  hourly rate page
            try:
                hourly_input = driver.find_element(By.XPATH, '//*[@id="main"]/div/div/div[2]/div/div/div/div[2]/div/div/div[3]/div/div/div/div[2]/div[1]/div/div[2]/div/div/div/input')
                hourly_input.clear()
                hourly_input.send_keys(my_rate)
                time.sleep(1)
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
        #     profile page
            try:
                upload_btn = driver.find_element(By.XPATH, '//*[@id="main"]/div/div/div[2]/div/div/div/div[2]/div/div/div[3]/div/div/div/div/div[1]/div/div/div/div/button')
                driver.execute_script("arguments[0].click();", upload_btn)
                time.sleep(2)
                file_input = driver.find_element(By.XPATH,
                                                 '/html/body/div[5]/div[2]/div/div/div[2]/div/div[2]/div[1]/div[1]/input')
                file_input.send_keys(os.getcwd() + "/avatar.png")
                time.sleep(2)
                attach_btn = driver.find_element(By.XPATH, '/html/body/div[5]/div[2]/div/div/div[3]/div/button[2]')
                driver.execute_script("arguments[0].click();", attach_btn)
                time.sleep(5)
                street_input = driver.find_element(By.XPATH, '//*[@id="main"]/div/div/div[2]/div/div/div/div[2]/div/div/div[3]/div/div/div/div/div[2]/div[1]/div/div[2]/div[1]/div[1]/div/input')
                street_input.send_keys(address)
                time.sleep(1)
                apt_input = driver.find_element(By.XPATH, '//*[@id="main"]/div/div/div[2]/div/div/div/div[2]/div/div/div[3]/div/div/div/div/div[2]/div[1]/div/div[2]/div[1]/div[2]/div/input')
                apt_input.send_keys(apt)
                time.sleep(1)
                # select the city
                city_input = driver.find_elements(By.CLASS_NAME, 'air3-input')[2]
                driver.execute_script("arguments[0].click();", city_input)
                time.sleep(1)
                fake_input = driver.find_element(By.CLASS_NAME, 'air3-typeahead-input-main')
                fake_input.send_keys(city)
                time.sleep(2)
                city_dropdown = driver.find_element(By.CLASS_NAME, "air3-menu-list")
                city_item = city_dropdown.find_elements(By.CLASS_NAME, "air3-menu-item")[0]
                driver.execute_script("arguments[0].click();", city_item)
                time.sleep(2)
                # input state
                state_input = driver.find_elements(By.CLASS_NAME, 'air3-input')[3]
                state_input.send_keys(my_state)
                time.sleep(1)
                # input phone
                phone_input = driver.find_elements(By.CLASS_NAME, 'air3-input')[4]
                phone_input.send_keys(phone)
                time.sleep(1)
                next_btn = driver.find_element(By.XPATH,
                                               '//*[@id="main"]/div/div/div[2]/div/div/div/div[3]/div[4]/button[2]')
                driver.execute_script("arguments[0].click();", next_btn)
                # next_btn.click()
                time.sleep(2)                
            except Exception as e:
                print(e)
                pass
            try:
                submit_btn = driver.find_element(By.CLASS_NAME, 'air3-btn-primary')
                driver.execute_script("arguments[0].click();", submit_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass
            try:
                browse_btn = driver.find_element(By.CLASS_NAME, 'air3-btn-secondary')
                driver.execute_script("arguments[0].click();", browse_btn)
                time.sleep(2)
            except Exception as e:
                print(e)
                pass

        except Exception as e:
            print("you didn't put verify link")
            pass
        print("profile done")
        driver.close()
        driver.quit()

    except Exception as e:
        print(e)


if __name__ == "__main__":
    main()
