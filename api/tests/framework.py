""" Helper functions for the tests """

def assert_status_code(response, code):
    """ Prints the response if the status code is not valid """
    if not response.status_code == code:
        print(response)
        print(response.text)
    assert response.status_code == code
